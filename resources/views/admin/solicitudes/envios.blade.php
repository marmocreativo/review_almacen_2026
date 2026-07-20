<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Envíos</h2>
            <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-primary btn-sm gap-1">
                <x-heroicon-o-plus class="w-4 h-4" />
                <span class="hidden sm:inline">Nueva solicitud</span>
                <span class="sm:hidden">Nueva</span>
            </a>
        </div>
    </x-slot>

    <x-alert />

    {{-- FILTROS --}}
    <form method="GET" class="card bg-base-100 shadow mb-4">
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="form-control flex-1 min-w-[200px]">
                    <label class="label py-0"><span class="label-text text-xs">Buscar</span></label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                        placeholder="Empresa o sede..."
                        class="input input-bordered input-sm w-full" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-funnel class="w-4 h-4" />
                        Filtrar
                    </button>
                    <a href="{{ route('admin.envios.index') }}" class="btn btn-ghost btn-sm gap-1">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                        Limpiar
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card md:bg-base-100 md:shadow">
        <div class="card-body p-0">

            {{-- VISTA DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Empresa / Sede</th>
                            <th>Responsable</th>
                            <th>Fecha solicitud</th>
                            <th class="text-center">Exámenes</th>
                            <th class="text-center">Artículos</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr>
                                <td class="font-mono text-sm">{{ $solicitud->ID_SOLICITUD }}</td>
                                <td>
                                    <p class="font-medium">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                    <p class="text-sm text-base-content/60">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                </td>
                                <td>
                                    <p>{{ $solicitud->RESPONSABLE_NOMBRE }}</p>
                                    <p class="text-sm text-base-content/60">{{ $solicitud->RESPONSABLE_CORREO }}</p>
                                </td>
                                <td class="text-sm">{{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}</td>
                                <td class="text-center font-mono">{{ $solicitud->examenes_count }}</td>
                                <td class="text-center font-mono">{{ $solicitud->articulos_count }}</td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <a href="{{ route('admin.solicitudes.edit', $solicitud->ID_SOLICITUD) }}"
                                            class="btn btn-primary btn-xs gap-1" title="Armar envío">
                                            <x-heroicon-o-truck class="w-4 h-4" />
                                            Armar envío
                                        </a>
                                        <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                            class="btn btn-outline btn-xs" title="Ver detalle">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.solicitudes.destroy', $solicitud->ID_SOLICITUD) }}"
                                            onsubmit="return confirm('¿Eliminar esta solicitud? Se revertirán todas las cantidades.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-error btn-xs" title="Eliminar">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-base-content/50 py-8">
                                    No hay solicitudes pendientes de envío.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL: cards --}}
            <div class="md:hidden">
                @forelse($solicitudes as $solicitud)
                    <div class="px-3 pt-6 pb-3">
                        <div class="relative">
                            <div class="absolute -top-6 left-0 h-6 px-3 flex items-center gap-1.5
                                        rounded-t-lg border border-b-0 text-xs font-medium bg-warning/20 text-warning-content border-warning/40">
                                <x-heroicon-o-truck class="w-3.5 h-3.5" />
                                Solicitud #{{ $solicitud->ID_SOLICITUD }}
                            </div>

                            <div class="bg-base-100 rounded-b-xl rounded-tr-xl border border-warning/40 p-3">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm truncate">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-base-content/50 truncate">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-base-content/40 truncate">{{ $solicitud->RESPONSABLE_NOMBRE }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs text-base-content/50">
                                            {{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}
                                        </p>
                                        <p class="text-xs font-mono text-base-content/40 mt-0.5">
                                            {{ $solicitud->examenes_count }} exám.
                                        </p>
                                        <p class="text-xs font-mono text-base-content/40">
                                            {{ $solicitud->articulos_count }} art.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('admin.solicitudes.edit', $solicitud->ID_SOLICITUD) }}"
                                        class="btn btn-primary btn-sm gap-1 flex-1">
                                        <x-heroicon-o-truck class="w-4 h-4" />
                                        Armar envío
                                    </a>
                                    <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                        class="btn btn-outline btn-sm">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    <form method="POST"
                                        action="{{ route('admin.solicitudes.destroy', $solicitud->ID_SOLICITUD) }}"
                                        onsubmit="return confirm('¿Eliminar esta solicitud? Se revertirán todas las cantidades.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-error btn-sm">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay solicitudes pendientes de envío.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $solicitudes->links() }}</div>

</x-app-layout>