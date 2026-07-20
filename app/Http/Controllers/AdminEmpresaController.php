<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class AdminEmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::withCount('sedes')->latest();

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'like', "%$b%")
                ->orWhere('razon_social', 'like', "%$b%")
                ->orWhere('rfc', 'like', "%$b%");
            });
        }

        $empresas = $query->paginate(15)->withQueryString();
        return view('admin.empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('admin.empresas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'rfc'          => 'nullable|string|max:255',
            'logo'         => 'nullable|image|max:2048',
            'estado'       => 'required|in:activo,inactivo',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Empresa::create($validated);

        return redirect()->route('admin.empresas.index')
            ->with('success', 'Empresa creada correctamente.');
    }

    public function show(Empresa $empresa)
    {
        $empresa->load([
            'sedes.contactos',
        ]);

        $solicitudes = \App\Models\Solicitud::where('ID_EMPRESA', $empresa->id)
            ->with('sede')
            ->withCount('examenes')
            ->orderBy('FECHA_SOLICITUD', 'desc')
            ->paginate(10);

        return view('admin.empresas.show', compact('empresa', 'solicitudes'));
    }

    public function edit(Empresa $empresa)
    {
        return view('admin.empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'rfc'          => 'nullable|string|max:255',
            'logo'         => 'nullable|image|max:2048',
            'estado'       => 'required|in:activo,inactivo',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa->update($validated);

        return redirect()->route('admin.empresas.index')
            ->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return redirect()->route('admin.empresas.index')
            ->with('success', 'Empresa eliminada correctamente.');
    }
}