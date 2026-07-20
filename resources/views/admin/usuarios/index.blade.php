<x-app-layout>
    <div class="max-w-5xl mx-auto py-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Usuarios</h1>
                <p class="text-base-content/60">Administra los usuarios del sistema</p>
            </div>
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                <x-heroicon-o-plus class="w-4 h-4" />
                Nuevo usuario
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="mb-4">
            <div class="join w-full max-w-md">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Buscar por nombre o correo..."
                    class="input input-bordered join-item w-full"
                >
                <button type="submit" class="btn btn-outline join-item">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </button>
                @if (request('q'))
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline join-item">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </a>
                @endif
            </div>
        </form>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto bg-base-100 rounded-box shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Correo electrónico</th>
                        <th>Rol</th>
                        <th>Registrado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar avatar-placeholder">
                                        <div class="bg-primary text-primary-content w-10 rounded-full flex items-center justify-center">
                                            <span>{{ Str::upper(Str::substr($usuario->name, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <span class="font-medium truncate">{{ $usuario->name }}</span>
                                </div>
                            </td>
                            <td class="truncate">{{ $usuario->email }}</td>
                            <td>
                                <span class="badge badge-sm badge-outline">
                                    {{ $usuario->userRole?->role?->rol ? ucfirst($usuario->userRole->role->rol) : 'Sin rol' }}
                                </span>
                            </td>
                            <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-square btn-outline btn-info btn-sm">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    @if ($usuario->id !== auth()->id())
                                        <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar a {{ $usuario->name }}? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-square btn-outline btn-error btn-sm">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-base-content/60 py-6">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden space-y-3">
            @forelse ($usuarios as $usuario)
                <div class="card md:bg-base-100 md:shadow bg-base-100 shadow">
                    <div class="card-body p-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="avatar avatar-placeholder shrink-0">
                                <div class="bg-primary text-primary-content w-10 rounded-full flex items-center justify-center">
                                    <span>{{ Str::upper(Str::substr($usuario->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium truncate">{{ $usuario->name }}</p>
                                <p class="text-sm text-base-content/60 truncate">{{ $usuario->email }}</p>
                                <span class="badge badge-xs badge-outline mt-1">
                                    {{ $usuario->userRole?->role?->rol ? ucfirst($usuario->userRole->role->rol) : 'Sin rol' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-base-200">
                            <span class="text-xs text-base-content/50">
                                Registrado {{ $usuario->created_at->format('d/m/Y') }}
                            </span>
                            <div class="flex gap-2 shrink-0">
                                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-square btn-outline btn-info btn-sm">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                @if ($usuario->id !== auth()->id())
                                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar a {{ $usuario->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-square btn-outline btn-error btn-sm">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-base-content/60 py-6">
                    No se encontraron usuarios.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $usuarios->links() }}
        </div>

    </div>
</x-app-layout>