<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminRolController extends Controller
{
    /**
     * Catálogo de secciones disponibles en el sistema (clave => etiqueta visible).
     * Si agregas un módulo nuevo, solo agrégalo aquí.
     */
    public const SECCIONES = [
        'dashboard'     => 'Dashboard',
        'inventario'    => 'Inventario',
        'solicitudes'   => 'Solicitudes',
        'envios'        => 'Envíos',
        'devoluciones'  => 'Devoluciones',
        'facturacion'   => 'Facturación',
        'cobranza'      => 'Cobranza',
        'destruccion'   => 'Destrucción',
        'ordenes'       => 'Órdenes de Compra',
        'empresas'      => 'Empresas',
        'tipo-examenes' => 'Tipos de examen',
        'usuarios'      => 'Usuarios',
    ];

    public function index()
    {
        $roles = Role::withCount('usuarios')->orderBy('rol')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create', ['secciones' => self::SECCIONES]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rol'      => 'required|string|max:255|unique:roles,rol',
            'permisos' => 'array',
            'permisos.*' => Rule::in(array_keys(self::SECCIONES)),
        ]);

        Role::create([
            'rol'      => $validated['rol'],
            'permisos' => $validated['permisos'] ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', ['role' => $role, 'secciones' => self::SECCIONES]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'rol'      => ['required', 'string', 'max:255', Rule::unique('roles', 'rol')->ignore($role->id)],
            'permisos' => 'array',
            'permisos.*' => Rule::in(array_keys(self::SECCIONES)),
        ]);

        $role->update([
            'rol'      => $validated['rol'],
            'permisos' => $validated['permisos'] ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->usuarios()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay usuarios asignados a este rol.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}