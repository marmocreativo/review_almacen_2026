<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Solicitudes</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.solicitudes.exportar', request()->query()) }}" class="btn btn-success btn-sm gap-1">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    <span class="hidden sm:inline">Exportar</span>
                </a>
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
            <div class="flex flex-wrap gap-2 items-end">
                <div class="form-control flex-1 min-w-[180px]">
                    <label class="label py-0"><span class="label-text text-xs">Buscar</span></label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                        placeholder="Empresa o sede..."
                        class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control w-48">
                    <label class="label py-0"><span class="label-text text-xs">Cliente</span></label>
                    <select name="empresa" class="select select-bordered select-sm w-full">
                        <option value="">Todos</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}" {{ request('empresa') == $empresa->id ? 'selected' : '' }}>
                                {{ $empresa->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-36">
                    <label class="label py-0"><span class="label-text text-xs">Estado</span></label>
                    <select name="estado" class="select select-bordered select-sm w-full">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="enviada" {{ request('estado') === 'enviada' ? 'selected' : '' }}>Enviada</option>
                        <option value="retornada" {{ request('estado') === 'retornada' ? 'selected' : '' }}>Retornada</option>
                    </select>
                </div>
                <div class="form-control w-40">
                    <label class="label py-0"><span class="label-text text-xs">Desde</span></label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control w-40">
                    <label class="label py-0"><span class="label-text text-xs">Hasta</span></label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control w-40">
                    <label class="label py-0"><span class="label-text text-xs">Ordenar por</span></label>
                    <select name="orden" class="select select-bordered select-sm w-full">
                        <option value="FECHA_SOLICITUD" {{ request('orden', 'FECHA_SOLICITUD') === 'FECHA_SOLICITUD' ? 'selected' : '' }}>Fecha</option>
                        <option value="EMPRESA" {{ request('orden') === 'EMPRESA' ? 'selected' : '' }}>Cliente</option>
                        <option value="ESTADO" {{ request('orden') === 'ESTADO' ? 'selected' : '' }}>Estado</option>
                    </select>
                </div>
                <div class="form-control w-32">
                    <label class="label py-0"><span class="label-text text-xs">Dirección</span></label>
                    <select name="dir" class="select select-bordered select-sm w-full">
                        <option value="desc" {{ request('dir', 'desc') === 'desc' ? 'selected' : '' }}>Descendente</option>
                        <option value="asc" {{ request('dir') === 'asc' ? 'selected' : '' }}>Ascendente</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm gap-1">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    Filtrar
                </button>
                <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-ghost btn-sm gap-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    Limpiar
                </a>
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
                            <th>Cliente / Sede</th>
                            <th>Responsable</th>
                            <th>Fecha</th>
                            <th class="text-center">Exámenes</th>
                            <th class="text-center">Artículos</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            @php
                                $badge = match($solicitud->ESTADO_SOLICITUD) {
                                    'pendiente' => 'badge-warning',
                                    'enviada'   => 'badge-info',
                                    'retornada' => 'badge-success',
                                    default     => 'badge-ghost',
                                };
                                $filaClase = match($solicitud->APROBACION) {
                                    'pendiente' => 'bg-warning/10',
                                    'cancelada' => 'opacity-40',
                                    default     => '',
                                };
                            @endphp
                            <tr class="{{ $filaClase }}">
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
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="badge {{ $badge }}">{{ ucfirst($solicitud->ESTADO_SOLICITUD) }}</span>
                                        @if($solicitud->APROBACION === 'pendiente')
                                            <span class="badge badge-xs badge-warning">Aprobación pendiente</span>
                                        @elseif($solicitud->APROBACION === 'cancelada')
                                            <span class="badge badge-xs badge-error">Cancelada</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                            class="btn btn-outline btn-info btn-xs gap-1">
                                            <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                            Ver
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.solicitudes.destroy', $solicitud->ID_SOLICITUD) }}"
                                            onsubmit="return confirm('¿Eliminar esta solicitud? Se revertirán todas las cantidades.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-error btn-xs">
                                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-base-content/50 py-8">
                                    No hay solicitudes registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL --}}
            <div class="md:hidden divide-y divide-base-300">
                @forelse($solicitudes as $solicitud)
                    @php
                        $badge = match($solicitud->ESTADO_SOLICITUD) {
                            'pendiente' => 'badge-warning',
                            'enviada'   => 'badge-info',
                            'retornada' => 'badge-success',
                            default     => 'badge-ghost',
                        };
                        $filaClase = match($solicitud->APROBACION) {
                            'pendiente' => 'bg-warning/10',
                            'cancelada' => 'opacity-40',
                            default     => '',
                        };
                    @endphp
                    <div class="px-4 py-3 {{ $filaClase }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono text-xs text-base-content/50">#{{ $solicitud->ID_SOLICITUD }}</span>
                                    <span class="badge badge-xs {{ $badge }}">{{ ucfirst($solicitud->ESTADO_SOLICITUD) }}</span>
                                    @if($solicitud->APROBACION === 'pendiente')
                                        <span class="badge badge-xs badge-warning">Aprobación pendiente</span>
                                    @elseif($solicitud->APROBACION === 'cancelada')
                                        <span class="badge badge-xs badge-error">Cancelada</span>
                                    @endif
                                </div>
                                <p class="font-semibold text-sm truncate mt-0.5">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                <p class="text-xs text-base-content/50 truncate">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                <p class="text-xs text-base-content/40 mt-1">
                                    {{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}
                                    · {{ $solicitud->examenes_count }} exám. · {{ $solicitud->articulos_count }} art.
                                </p>
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                    class="btn btn-ghost btn-xs btn-square">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>
                                <form method="POST"
                                    action="{{ route('admin.solicitudes.destroy', $solicitud->ID_SOLICITUD) }}"
                                    onsubmit="return confirm('¿Eliminar esta solicitud? Se revertirán todas las cantidades.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs btn-square text-error">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">No hay solicitudes registradas.</div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $solicitudes->links() }}</div>
</x-app-layout>