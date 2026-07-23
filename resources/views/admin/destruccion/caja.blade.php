<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.destruccion.index') }}" class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <h2 class="text-xl font-semibold">Destrucción — {{ $caja->NOMBRE }}</h2>
                @if($caja->estaDestruida())
                    <span class="badge badge-error">Destruida</span>
                @elseif($caja->estaCerrada())
                    <span class="badge badge-warning">Cerrada</span>
                @else
                    <span class="badge badge-success">Abierta</span>
                @endif
            </div>
            <div class="flex gap-2">
                @if(!$caja->estaCerrada())
                    <form method="POST" action="{{ route('admin.destruccion.cerrar', $caja) }}"
                        onsubmit="return confirm('¿Cerrar esta caja? Ya no se podrán agregar más artículos.')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-warning btn-sm">Cerrar caja</button>
                    </form>
                @elseif(!$caja->estaDestruida())
                    <form method="POST" action="{{ route('admin.destruccion.destruida', $caja) }}"
                        onsubmit="return confirm('¿Marcar esta caja como destruida?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-error btn-sm">Marcar destruida</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <x-alert />

    <form method="GET" class="card bg-base-100 shadow mb-4">
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Buscar nombre</span></label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                        placeholder="Nombre del artículo..."
                        class="input input-bordered input-sm w-48" />
                </div>
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Folio</span></label>
                    <input type="text" name="folio" value="{{ request('folio') }}"
                        class="input input-bordered input-sm w-28" />
                </div>
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Serie</span></label>
                    <input type="text" name="serie" value="{{ request('serie') }}"
                        class="input input-bordered input-sm w-28" />
                </div>
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Formato</span></label>
                    <input type="text" name="formato" value="{{ request('formato') }}"
                        class="input input-bordered input-sm w-28" />
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                <a href="{{ route('admin.destruccion.caja', $caja) }}" class="btn btn-ghost btn-sm">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra table-sm">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Serie</th>
                            <th>Nombre</th>
                            <th>Formato</th>
                            <th class="text-center">Cant. destrucción</th>
                            <th>Fecha retorno</th>
                            <th>Solicitud</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articulos as $item)
                            <tr>
                                <td class="font-mono text-sm">{{ $item->FOLIO }}</td>
                                <td class="font-mono text-sm">{{ $item->SERIE ?: '—' }}</td>
                                <td>{{ $item->NOMBRE }}</td>
                                <td>{{ $item->FORMATO ?: '—' }}</td>
                                <td class="text-center">{{ $item->CANTIDAD_A_DESTRUCCION }}</td>
                                <td>{{ $item->FECHA_RETORNO ? \Carbon\Carbon::parse($item->FECHA_RETORNO)->format('d/m/Y') : '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.solicitudes.devolucion.show', $item->ID_SOLICITUD) }}"
                                        class="badge badge-ghost badge-sm">#{{ $item->ID_SOLICITUD }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-base-content/50 py-8">
                                    No hay artículos en esta caja.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $articulos->links() }}</div>
</x-app-layout>