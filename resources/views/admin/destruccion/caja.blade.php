<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.destruccion.index') }}" class="btn btn-ghost btn-sm">←</a>
            <h2 class="text-xl font-semibold">Destrucción — {{ $caja ?: '(Sin ubicación)' }}</h2>
        </div>
    </x-slot>

    <form method="GET" class="card bg-base-100 shadow mb-4">
        <input type="hidden" name="caja" value="{{ $caja }}" />
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Buscar nombre</span></label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                        placeholder="Nombre del artículo..."
                        class="input input-bordered input-sm w-48" />
                </div>
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Desde</span></label>
                    <input type="date" name="fecha_desde"
                        value="{{ request('fecha_desde', now()->subMonth()->format('Y-m-d')) }}"
                        class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Hasta</span></label>
                    <input type="date" name="fecha_hasta"
                        value="{{ request('fecha_hasta', now()->format('Y-m-d')) }}"
                        class="input input-bordered input-sm" />
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
                <a href="{{ route('admin.destruccion.caja', ['caja' => $caja]) }}"
                    class="btn btn-ghost btn-sm">Limpiar</a>
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
                                    <a href="{{ route('admin.solicitudes.edit', $item->ID_SOLICITUD) }}"
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