<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contacto;
use App\Models\Empresa;
use App\Models\Sede;
use Illuminate\Http\Request;

class AdminContactoController extends Controller
{
    public function index(Empresa $empresa, Sede $sede)
    {
        $contactos = $sede->contactos()->latest()->paginate(15);
        return view('admin.contactos.index', compact('empresa', 'sede', 'contactos'));
    }

    public function create(Empresa $empresa, Sede $sede)
    {
        return view('admin.contactos.create', compact('empresa', 'sede'));
    }

    public function store(Request $request, Empresa $empresa, Sede $sede)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'correo'    => 'nullable|email|max:255',
        ]);

        $validated['id_sede'] = $sede->id;
        Contacto::create($validated);

        return redirect()->route('admin.empresas.sedes.contactos.index', [$empresa, $sede])
            ->with('success', 'Contacto creado correctamente.');
    }

    public function edit(Empresa $empresa, Sede $sede, Contacto $contacto)
    {
        return view('admin.contactos.edit', compact('empresa', 'sede', 'contacto'));
    }

    public function update(Request $request, Empresa $empresa, Sede $sede, Contacto $contacto)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'correo'    => 'nullable|email|max:255',
        ]);

        $contacto->update($validated);

        return redirect()->route('admin.empresas.sedes.contactos.index', [$empresa, $sede])
            ->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy(Empresa $empresa, Sede $sede, Contacto $contacto)
    {
        $contacto->delete();
        return redirect()->route('admin.empresas.sedes.contactos.index', [$empresa, $sede])
            ->with('success', 'Contacto eliminado correctamente.');
    }
}