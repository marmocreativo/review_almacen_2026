<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Facturación</h2>
            <a href="{{ route('admin.facturacion.exportar', request()->query()) }}" class="btn btn-success btn-sm gap-1">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                <span class="hidden sm:inline">Exportar</span>
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
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Factura</span></label>
                    <select name="estado_factura" class="select select-bordered select-sm">
                        <option value="">Todas</option>
                        <option value="pendiente" {{ request('estado_factura') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="facturada" {{ request('estado_factura') === 'facturada' ? 'selected' : '' }}>Facturada</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-funnel class="w-4 h-4" />
                        Filtrar
                    </button>
                    <a href="{{ route('admin.facturacion.index') }}" class="btn btn-ghost btn-sm gap-1">
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
                            <th>Fecha solicitud</th>
                            <th class="text-right">Importe</th>
                            <th>Estado factura</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            @php $facturada = $solicitud->ESTADO_FACTURA === 'facturada'; @endphp
                            <tr>
                                <td class="font-mono text-sm">{{ $solicitud->ID_SOLICITUD }}</td>
                                <td>
                                    <p class="font-medium">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                    <p class="text-sm text-base-content/60">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                </td>
                                <td class="text-sm">{{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}</td>
                                <td class="text-right font-mono">
                                    {{ $solicitud->IMPORTE_FACTURA ? '$' . number_format($solicitud->IMPORTE_FACTURA, 2) : '—' }}
                                </td>
                                <td>
                                    <span class="badge {{ $facturada ? 'badge-success' : 'badge-ghost' }}">
                                        {{ ucfirst($solicitud->ESTADO_FACTURA) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                            class="btn {{ $facturada ? 'btn-outline' : 'btn-primary' }} btn-xs gap-1"
                                            title="{{ $facturada ? 'Ver factura' : 'Adjuntar factura' }}">
                                            <x-heroicon-o-document-currency-dollar class="w-4 h-4" />
                                            {{ $facturada ? 'Ver' : 'Facturar' }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-base-content/50 py-8">
                                    No hay solicitudes en facturación.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL: cards --}}
            <div class="md:hidden">
                @forelse($solicitudes as $solicitud)
                    @php
                        $facturada = $solicitud->ESTADO_FACTURA === 'facturada';
                        $tabClasses = $facturada ? 'bg-success/20 text-success-content border-success/40' : 'bg-base-200 text-base-content border-base-300';
                        $cardBorder = $facturada ? 'border-success/40' : 'border-base-300';
                    @endphp
                    <div class="px-3 pt-6 pb-3">
                        <div class="relative">
                            <div class="absolute -top-6 left-0 h-6 px-3 flex items-center gap-1.5
                                        rounded-t-lg border border-b-0 text-xs font-medium {{ $tabClasses }}">
                                <x-heroicon-o-document-currency-dollar class="w-3.5 h-3.5" />
                                Solicitud #{{ $solicitud->ID_SOLICITUD }}
                            </div>

                            <div class="bg-base-100 rounded-b-xl rounded-tr-xl border {{ $cardBorder }} p-3">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm truncate">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-base-content/50 truncate">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-base-content/40 mt-1">
                                            {{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="font-mono text-sm font-semibold">
                                            {{ $solicitud->IMPORTE_FACTURA ? '$' . number_format($solicitud->IMPORTE_FACTURA, 2) : '—' }}
                                        </p>
                                        <span class="badge badge-xs {{ $facturada ? 'badge-success' : 'badge-ghost' }} mt-1">
                                            {{ ucfirst($solicitud->ESTADO_FACTURA) }}
                                        </span>
                                    </div>
                                </div>

                                <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                    class="btn {{ $facturada ? 'btn-outline' : 'btn-primary' }} btn-sm gap-1 w-full">
                                    <x-heroicon-o-document-currency-dollar class="w-4 h-4" />
                                    {{ $facturada ? 'Ver factura' : 'Adjuntar factura' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay solicitudes en facturación.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $solicitudes->links() }}</div>

</x-app-layout>