<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Empresas</h2>
            <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary btn-sm gap-1">
                <x-heroicon-o-plus class="w-4 h-4" />
                <span class="hidden sm:inline">Nueva empresa</span>
                <span class="sm:hidden">Nueva</span>
            </a>
        </div>
    </x-slot>

    {{-- FILTROS --}}
    <form method="GET" class="card bg-base-100 shadow mb-4">
        <div class="card-body py-3">
            <div class="flex gap-2 items-end">
                <div class="form-control flex-1">
                    <label class="label py-0"><span class="label-text text-xs">Buscar</span></label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                        placeholder="Nombre, razón social o RFC..."
                        class="input input-bordered input-sm w-full" />
                </div>
                <button type="submit" class="btn btn-primary btn-sm gap-1">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    <span class="hidden sm:inline">Filtrar</span>
                </button>
                <a href="{{ route('admin.empresas.index') }}" class="btn btn-ghost btn-sm gap-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    <span class="hidden sm:inline">Limpiar</span>
                </a>
            </div>
        </div>
    </form>

    <x-alert />

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">

            {{-- VISTA DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Razón social</th>
                            <th>RFC</th>
                            <th>Sedes</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $empresa)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-10 h-10">
                                                <img src="{{ asset('storage/' . $empresa->logo) }}"
                                                    onerror="this.src='https://placehold.co/40x40?text=E'"
                                                    alt="{{ $empresa->nombre }}" />
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.empresas.show', $empresa) }}"
                                            class="font-medium hover:text-primary">
                                            {{ $empresa->nombre }}
                                        </a>
                                    </div>
                                </td>
                                <td>{{ $empresa->razon_social ?? '—' }}</td>
                                <td class="font-mono text-sm">{{ $empresa->rfc ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.empresas.sedes.index', $empresa) }}"
                                        class="badge badge-ghost">
                                        {{ $empresa->sedes_count }} sedes
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $empresa->estado === 'activo' ? 'badge-success' : 'badge-error' }}">
                                        {{ $empresa->estado }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <a href="{{ route('admin.empresas.show', $empresa) }}"
                                            class="btn btn-ghost btn-xs gap-1">
                                            <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.empresas.edit', $empresa) }}"
                                            class="btn btn-info btn-xs gap-1">
                                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                            Editar
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.empresas.destroy', $empresa) }}"
                                            onsubmit="return confirm('¿Eliminar esta empresa?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-error btn-xs">
                                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-base-content/50 py-8">
                                    No hay empresas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL --}}
            <div class="md:hidden divide-y divide-base-300">
                @forelse($empresas as $empresa)
                    <div class="flex items-center gap-3 px-4 py-3">

                        {{-- Avatar --}}
                        <div class="avatar shrink-0">
                            <div class="mask mask-squircle w-11 h-11">
                                <img src="{{ asset('storage/' . $empresa->logo) }}"
                                    onerror="this.src='https://placehold.co/44x44?text=E'"
                                    alt="{{ $empresa->nombre }}" />
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.empresas.show', $empresa) }}"
                                class="font-semibold text-sm truncate block hover:text-primary">
                                {{ $empresa->nombre }}
                            </a>
                            <p class="font-mono text-xs text-base-content/50 truncate">
                                {{ $empresa->rfc ?? '—' }}
                            </p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <a href="{{ route('admin.empresas.sedes.index', $empresa) }}"
                                    class="badge badge-ghost badge-sm">
                                    {{ $empresa->sedes_count }} sedes
                                </a>
                                <span class="badge badge-sm {{ $empresa->estado === 'activo' ? 'badge-success' : 'badge-error' }}">
                                    {{ $empresa->estado }}
                                </span>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex gap-1 shrink-0">
                            <a href="{{ route('admin.empresas.edit', $empresa) }}"
                                class="btn btn-ghost btn-xs btn-square">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                            </a>
                            <form method="POST"
                                action="{{ route('admin.empresas.destroy', $empresa) }}"
                                onsubmit="return confirm('¿Eliminar esta empresa?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-xs btn-square text-error">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay empresas registradas.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $empresas->links() }}</div>

</x-app-layout>