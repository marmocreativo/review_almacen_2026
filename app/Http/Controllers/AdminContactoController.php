<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contacto;
use App\Models\Empresa;
use Illuminate\Http\Request;

class AdminContactoController extends Controller
{
    public function index(Empresa $empresa)
    {
        $contactos = $empresa->contactos()->with('sedes')->latest()->paginate(15);
        return view('admin.contactos.index', compact('empresa', 'contactos'));
    }

    public function create(Empresa $empresa)
    {
        $sedes = $empresa->sedes()->where('estado', 'activo')->orderBy('nombre')->get();
        return view('admin.contactos.create', compact('empresa', 'sedes'));
    }

    public function store(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'correo'    => 'nullable|email|max:255',
            'sedes'     => 'nullable|array',
            'sedes.*'   => 'exists:sedes,id',
        ]);

        $validated['id_empresa'] = $empresa->id;
        $validated['pin'] = \App\Models\Contacto::generarPinUnico();
        $sedes = $validated['sedes'] ?? [];
        unset($validated['sedes']);

        $contacto = Contacto::create($validated);
        $contacto->sedes()->sync($sedes);

        if ($request->wantsJson()) {
            $contacto->load('sedes');
            return response()->json(['success' => true, 'contacto' => $contacto]);
        }

        return redirect()->route('admin.empresas.contactos.index', $empresa)
            ->with('success', "Contacto creado correctamente. PIN de acceso: {$contacto->pin}");
    }

    public function edit(Empresa $empresa, Contacto $contacto)
    {
        $sedes = $empresa->sedes()->where('estado', 'activo')->orderBy('nombre')->get();
        $contacto->load('sedes');
        return view('admin.contactos.edit', compact('empresa', 'contacto', 'sedes'));
    }

    public function update(Request $request, Empresa $empresa, Contacto $contacto)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'correo'    => 'nullable|email|max:255',
            'sedes'     => 'nullable|array',
            'sedes.*'   => 'exists:sedes,id',
        ]);

        $sedes = $validated['sedes'] ?? [];
        unset($validated['sedes']);

        if (!$contacto->pin) {
            $validated['pin'] = Contacto::generarPinUnico();
        }

        $contacto->update($validated);
        $contacto->sedes()->sync($sedes);

        if ($request->wantsJson()) {
            $contacto->load('sedes');
            return response()->json(['success' => true, 'contacto' => $contacto]);
        }

        return redirect()->route('admin.empresas.contactos.index', $empresa)
            ->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy(Request $request, Empresa $empresa, Contacto $contacto)
    {
        $contacto->delete(); // el pivote se limpia solo por cascadeOnDelete

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.empresas.contactos.index', $empresa)
            ->with('success', 'Contacto eliminado correctamente.');
    }

    public function enviarPin(Request $request, Empresa $empresa, Contacto $contacto)
    {
        if (!$contacto->correo) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Este contacto no tiene correo electrónico registrado.'], 422);
            }
            return back()->with('error', 'Este contacto no tiene correo electrónico registrado.');
        }

        if (!$contacto->pin) {
            $contacto->update(['pin' => Contacto::generarPinUnico()]);
        }

        \Illuminate\Support\Facades\Mail::send(
            'admin.contactos.email-pin',
            ['contacto' => $contacto, 'empresa' => $empresa],
            function ($message) use ($contacto) {
                $message->to($contacto->correo)
                    ->subject('Tu PIN de acceso — ' . config('app.name'));
            }
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "PIN enviado a {$contacto->correo}."]);
        }

        return back()->with('success', "PIN enviado a {$contacto->correo}.");
    }

    public function json(Empresa $empresa)
    {
        $contactos = $empresa->contactos()->with('sedes')->latest()->get();
        return response()->json($contactos);
    }
}