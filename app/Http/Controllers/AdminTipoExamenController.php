<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TipoExamen;
use Illuminate\Http\Request;

class AdminTipoExamenController extends Controller
{
    public function index()
    {
        $tipos = TipoExamen::latest()->paginate(15);
        return view('admin.tipo-examenes.index', compact('tipos'));
    }

    public function create()
    {
        return view('admin.tipo-examenes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado'      => 'required|in:activo,inactivo',
            'candidatos_minimos' => 'required|integer|min:1',
            'dias_anticipacion'  => 'required|integer|min:1',
        ]);

        TipoExamen::create($validated);

        return redirect()->route('admin.tipo-examenes.index')
            ->with('success', 'Tipo de examen creado correctamente.');
    }

    public function edit(TipoExamen $tipoExamen)
    {
        return view('admin.tipo-examenes.edit', compact('tipoExamen'));
    }

    public function update(Request $request, TipoExamen $tipoExamen)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado'      => 'required|in:activo,inactivo',
            'candidatos_minimos' => 'required|integer|min:1',
            'dias_anticipacion'  => 'required|integer|min:1',
        ]);

        $tipoExamen->update($validated);

        return redirect()->route('admin.tipo-examenes.index')
            ->with('success', 'Tipo de examen actualizado correctamente.');
    }

    public function destroy(TipoExamen $tipoExamen)
    {
        $tipoExamen->delete();
        return redirect()->route('admin.tipo-examenes.index')
            ->with('success', 'Tipo de examen eliminado correctamente.');
    }
}