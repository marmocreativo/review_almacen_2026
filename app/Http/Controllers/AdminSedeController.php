<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Sede;
use Illuminate\Http\Request;

class AdminSedeController extends Controller
{
    public function index(Empresa $empresa)
    {
        $sedes = $empresa->sedes()->withCount('contactos')->latest()->paginate(15);
        return view('admin.sedes.index', compact('empresa', 'sedes'));
    }

    public function create(Empresa $empresa)
    {
        return view('admin.sedes.create', compact('empresa'));
    }

    public function store(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nombre'             => 'required|string|max:255',
            'calle_y_numero'     => 'nullable|string|max:255',
            'colonia_barrio'     => 'nullable|string|max:255',
            'alcaldia_municipio' => 'nullable|string|max:255',
            'ciudad'             => 'nullable|string|max:255',
            'estado_republica'   => 'nullable|string|max:255',
            'codigo_postal'      => 'nullable|string|max:10',
            'referencias'        => 'nullable|string',
            'estado'             => 'required|in:activo,inactivo',
        ]);

        $validated['id_empresa'] = $empresa->id;
        Sede::create($validated);

        return redirect()->route('admin.empresas.sedes.index', $empresa)
            ->with('success', 'Sede creada correctamente.');
    }

    public function show(Empresa $empresa, Sede $sede)
    {
        $sede->load('contactos');
        return view('admin.sedes.show', compact('empresa', 'sede'));
    }

    public function edit(Empresa $empresa, Sede $sede)
    {
        return view('admin.sedes.edit', compact('empresa', 'sede'));
    }

    public function update(Request $request, Empresa $empresa, Sede $sede)
    {
        $validated = $request->validate([
            'nombre'             => 'required|string|max:255',
            'calle_y_numero'     => 'nullable|string|max:255',
            'colonia_barrio'     => 'nullable|string|max:255',
            'alcaldia_municipio' => 'nullable|string|max:255',
            'ciudad'             => 'nullable|string|max:255',
            'estado_republica'   => 'nullable|string|max:255',
            'codigo_postal'      => 'nullable|string|max:10',
            'referencias'        => 'nullable|string',
            'estado'             => 'required|in:activo,inactivo',
        ]);

        $sede->update($validated);

        return redirect()->route('admin.empresas.sedes.index', $empresa)
            ->with('success', 'Sede actualizada correctamente.');
    }

    public function destroy(Empresa $empresa, Sede $sede)
    {
        $sede->delete();
        return redirect()->route('admin.empresas.sedes.index', $empresa)
            ->with('success', 'Sede eliminada correctamente.');
    }
}