<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Cobranza</h2>
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
                    <label class="label py-0"><span class="label-text text-xs">Estatus</span></label>
                    <select name="estado_cobranza" class="select select-bordered select-sm">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado_cobranza') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="vencido" {{ request('estado_cobranza') === 'vencido' ? 'selected' : '' }}>Vencido</option>
                        <option value="pagado" {{ request('estado_cobranza') === 'pagado' ? 'selected' : '' }}>Pagado</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-funnel class="w-4 h-4" />
                        Filtrar
                    </button>
                    <a href="{{ route('admin.cobranza.index') }}" class="btn btn-ghost btn-sm gap-1">
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
                            <th>Vencimiento</th>
                            <th class="text-right">Total factura</th>
                            <th class="text-right">Pagado</th>
                            <th class="text-right">Saldo</th>
                            <th>Estatus</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            @php $estado = $solicitud->estadoCobranza(); @endphp
                            <tr>
                                <td class="font-mono text-sm">{{ $solicitud->ID_SOLICITUD }}</td>
                                <td>
                                    <p class="font-medium">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                    <p class="text-sm text-base-content/60">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                </td>
                                <td class="text-sm">
                                    {{ $solicitud->FECHA_VENCIMIENTO_COBRANZA?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="text-right font-mono">${{ number_format($solicitud->IMPORTE_FACTURA, 2) }}</td>
                                <td class="text-right font-mono text-success">${{ number_format($solicitud->totalPagado(), 2) }}</td>
                                <td class="text-right font-mono {{ $solicitud->saldoPendiente() > 0 ? 'text-error' : '' }}">
                                    ${{ number_format($solicitud->saldoPendiente(), 2) }}
                                </td>
                                <td>
                                    <span class="badge {{ $estado['class'] }}">{{ $estado['label'] }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                        class="btn btn-primary btn-xs gap-1" title="Ver cobranza">
                                        <x-heroicon-o-banknotes class="w-4 h-4" />
                                        Cobranza
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-base-content/50 py-8">
                                    No hay solicitudes en cobranza.
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
                        $estado = $solicitud->estadoCobranza();
                        $tabClasses = match($estado['label']) {
                            'Pagado'  => 'bg-success/20 text-success-content border-success/40',
                            'Vencido' => 'bg-error/20 text-error-content border-error/40',
                            default   => 'bg-warning/20 text-warning-content border-warning/40',
                        };
                        $cardBorder = match($estado['label']) {
                            'Pagado'  => 'border-success/40',
                            'Vencido' => 'border-error/40',
                            default   => 'border-warning/40',
                        };
                    @endphp
                    <div class="px-3 pt-6 pb-3">
                        <div class="relative">
                            <div class="absolute -top-6 left-0 h-6 px-3 flex items-center gap-1.5
                                        rounded-t-lg border border-b-0 text-xs font-medium {{ $tabClasses }}">
                                <x-heroicon-o-banknotes class="w-3.5 h-3.5" />
                                Solicitud #{{ $solicitud->ID_SOLICITUD }}
                            </div>

                            <div class="bg-base-100 rounded-b-xl rounded-tr-xl border {{ $cardBorder }} p-3">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm truncate">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-base-content/50 truncate">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-base-content/40 mt-1">
                                            Vence: {{ $solicitud->FECHA_VENCIMIENTO_COBRANZA?->format('d/m/Y') ?? '—' }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="font-mono text-sm font-semibold text-error">
                                            ${{ number_format($solicitud->saldoPendiente(), 2) }}
                                        </p>
                                        <p class="text-xs text-base-content/40">saldo</p>
                                        <span class="badge badge-xs {{ $estado['class'] }} mt-1">{{ $estado['label'] }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                    class="btn btn-primary btn-sm gap-1 w-full">
                                    <x-heroicon-o-banknotes class="w-4 h-4" />
                                    Ver cobranza
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay solicitudes en cobranza.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $solicitudes->links() }}</div>

</x-app-layout>