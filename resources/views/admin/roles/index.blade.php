<x-app-layout>
    <div class="max-w-4xl mx-auto py-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Roles</h1>
                <p class="text-base-content/60">Administra los roles y sus permisos de acceso</p>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <x-heroicon-o-plus class="w-4 h-4" />
                Nuevo rol
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error mb-4">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto bg-base-100 rounded-box shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Secciones con acceso</th>
                        <th class="text-center">Usuarios</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td class="font-medium">{{ ucfirst($role->rol) }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1 max-w-md">
                                    @foreach($role->permisos as $permiso)
                                        <span class="badge badge-sm badge-outline">{{ \App\Http\Controllers\AdminRolController::SECCIONES[$permiso] ?? $permiso }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center font-mono">{{ $role->usuarios_count }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-square btn-outline btn-info btn-sm">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    @if($role->usuarios_count === 0)
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar el rol {{ $role->rol }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-square btn-outline btn-error btn-sm">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>