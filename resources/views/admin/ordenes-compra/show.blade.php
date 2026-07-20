<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('admin.ordenes.index') }}" class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <h2 class="text-xl font-semibold font-mono truncate">{{ $orden->ID_ORDEN }}</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.ordenes.articulos', $orden->ID_ORDEN) }}" class="btn btn-outline btn-sm gap-1">
                    <x-heroicon-o-archive-box class="w-4 h-4" />
                    <span class="hidden sm:inline">Artículos</span>
                </a>
                <a href="{{ route('admin.ordenes.edit', $orden->ID) }}" class="btn btn-info btn-sm gap-1">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    <span class="hidden sm:inline">Editar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <x-alert />

    <div class="space-y-4">

        {{-- Datos generales --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title text-base mb-3">Datos de la orden</h3>
                <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <dt class="text-xs text-base-content/50">ID Orden</dt>
                        <dd class="font-mono font-bold text-primary">{{ $orden->ID_ORDEN }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Folio factura</dt>
                        <dd class="font-mono">{{ $orden->FOLIO_FACTURA ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Fecha registro</dt>
                        <dd>{{ \Carbon\Carbon::parse($orden->FECHA_REGISTRO)->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Importe factura</dt>
                        <dd class="font-mono">
                            {{ $orden->IMPORTE_FACTURA ? '$' . number_format($orden->IMPORTE_FACTURA, 2) : '—' }}
                        </dd>
                    </div>
                </dl>

                @if($orden->FACTURA_PDF || $orden->FACTURA_XML)
                    <div class="mt-4 flex gap-2">
                        @if($orden->FACTURA_PDF)
                            <a href="{{ asset('storage/' . $orden->FACTURA_PDF) }}" target="_blank"
                                class="btn btn-error btn-sm gap-1">
                                <x-heroicon-o-document-arrow-down class="w-4 h-4" />
                                PDF
                            </a>
                        @endif
                        @if($orden->FACTURA_XML)
                            <a href="{{ asset('storage/' . $orden->FACTURA_XML) }}" target="_blank"
                                class="btn btn-warning btn-sm gap-1">
                                <x-heroicon-o-code-bracket class="w-4 h-4" />
                                XML
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Artículos --}}
        <div class="card md:bg-base-100 md:shadow">
            <div class="card-body p-0">
                <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                    <h3 class="font-semibold text-base">
                        Artículos
                        <span class="badge badge-ghost ml-1">{{ $articulos->count() }}</span>
                    </h3>
                    <a href="{{ route('admin.ordenes.articulos', $orden->ID_ORDEN) }}"
                        class="btn btn-outline btn-xs gap-1">
                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                        Gestionar
                    </a>
                </div>

                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Serie</th>
                                <th>Nombre</th>
                                <th>Formato</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Costo unitario</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($articulos as $item)
                                <tr>
                                    <td class="font-mono">{{ $item->FOLIO }}</td>
                                    <td class="font-mono text-sm text-base-content/60">{{ $item->SERIE ?: '—' }}</td>
                                    <td>{{ $item->articulo?->NOMBRE ?? '—' }}</td>
                                    <td>{{ $item->FORMATO ?: '—' }}</td>
                                    <td class="text-right font-mono">{{ $item->CANTIDAD }}</td>
                                    <td class="text-right font-mono">${{ number_format($item->COSTO_UNITARIO, 2) }}</td>
                                    <td class="text-right font-mono">${{ number_format($item->CANTIDAD * $item->COSTO_UNITARIO, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-base-content/50 py-8">
                                        No hay artículos en esta orden.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($articulos->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right font-semibold">Total</td>
                                    <td class="text-right font-mono font-bold">
                                        ${{ number_format($articulos->sum(fn($i) => $i->CANTIDAD * $i->COSTO_UNITARIO), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Móvil --}}
                <div class="md:hidden divide-y divide-base-300">
                    @forelse($articulos as $item)
                        <div class="flex items-center gap-3 px-4 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-mono text-sm font-semibold text-primary truncate">{{ $item->FOLIO }}</p>
                                @if($item->SERIE)
                                    <p class="font-mono text-xs text-base-content/50 truncate">{{ $item->SERIE }}</p>
                                @endif
                                <p class="text-sm truncate">{{ $item->articulo?->NOMBRE ?? '—' }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-mono text-sm font-bold">x{{ $item->CANTIDAD }}</p>
                                <p class="font-mono text-xs text-base-content/50">${{ number_format($item->COSTO_UNITARIO, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-base-content/50 py-8">
                            No hay artículos en esta orden.
                        </div>
                    @endforelse

                    @if($articulos->isNotEmpty())
                        <div class="flex justify-between items-center px-4 py-3 bg-base-200/60 font-semibold">
                            <span>Total</span>
                            <span class="font-mono">${{ number_format($articulos->sum(fn($i) => $i->CANTIDAD * $i->COSTO_UNITARIO), 2) }}</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</x-app-layout>