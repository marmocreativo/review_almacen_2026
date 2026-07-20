<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Búsqueda general</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">

        {{-- Input de búsqueda --}}
        <form method="GET" action="{{ route('dashboard.buscar') }}" class="mb-6">
            <div class="join w-full shadow">
                <input
                    type="search"
                    name="q"
                    value="{{ $q ?? '' }}"
                    placeholder="Buscar artículos, solicitudes, empresas, órdenes…"
                    autofocus
                    class="input input-bordered join-item flex-1 text-base"
                />
                <button type="submit" class="btn btn-primary join-item">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </button>
            </div>
        </form>

        {{-- Estado inicial --}}
        @if(is_null($resultados ?? null) && !isset($articulos))
            <div class="text-center py-16 text-base-content/40">
                <x-heroicon-o-magnifying-glass class="w-12 h-12 mx-auto mb-3 opacity-30" />
                <p>Escribe al menos 2 caracteres para buscar.</p>
            </div>

        {{-- Sin resultados --}}
        @elseif(isset($articulos) && $articulos->isEmpty() && $solicitudes->isEmpty() && $empresas->isEmpty() && $ordenes->isEmpty())
            <div class="text-center py-16 text-base-content/40">
                <x-heroicon-o-face-frown class="w-12 h-12 mx-auto mb-3 opacity-30" />
                <p>Sin resultados para <strong>{{ $q }}</strong>.</p>
            </div>

        @else

            {{-- Artículos --}}
            @if(isset($articulos) && $articulos->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-base-content/40 mb-2 px-1">
                    Artículos ({{ $articulos->count() }})
                </h3>
                <div class="card bg-base-100 shadow divide-y divide-base-200">
                    @foreach($articulos as $a)
                    <a href="{{ route('admin.articulos.show', $a) }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-base-200 transition-colors">
                        <x-heroicon-o-cube class="w-5 h-5 flex-shrink-0 text-base-content/30" />
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $a->NOMBRE }}</p>
                            <p class="text-xs text-base-content/50">
                                Folio: {{ $a->FOLIO }} · Serie: {{ $a->SERIE ?: '—' }} · Stock: {{ number_format($a->CANTIDAD_ALMACEN) }}
                            </p>
                        </div>
                        <x-heroicon-o-chevron-right class="w-4 h-4 text-base-content/30 flex-shrink-0" />
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Solicitudes --}}
            @if(isset($solicitudes) && $solicitudes->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-base-content/40 mb-2 px-1">
                    Solicitudes ({{ $solicitudes->count() }})
                </h3>
                <div class="card bg-base-100 shadow divide-y divide-base-200">
                    @foreach($solicitudes as $s)
                    <a href="{{ route('admin.solicitudes.show', $s) }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-base-200 transition-colors">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 flex-shrink-0 text-base-content/30" />
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">
                                #{{ $s->ID_SOLICITUD }} · {{ $s->empresa?->nombre ?? '—' }}
                            </p>
                            <p class="text-xs text-base-content/50">
                                {{ $s->RESPONSABLE_NOMBRE }} ·
                                {{ Carbon\Carbon::parse($s->FECHA_SOLICITUD)->format('d/m/Y') }} ·
                                <span class="badge badge-xs
                                    {{ $s->ESTADO_SOLICITUD === 'enviada' ? 'badge-info' : ($s->ESTADO_SOLICITUD === 'retornada' ? 'badge-success' : 'badge-warning') }}">
                                    {{ $s->ESTADO_SOLICITUD }}
                                </span>
                            </p>
                        </div>
                        <x-heroicon-o-chevron-right class="w-4 h-4 text-base-content/30 flex-shrink-0" />
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Empresas --}}
            @if(isset($empresas) && $empresas->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-base-content/40 mb-2 px-1">
                    Empresas ({{ $empresas->count() }})
                </h3>
                <div class="card bg-base-100 shadow divide-y divide-base-200">
                    @foreach($empresas as $e)
                    <a href="{{ route('admin.empresas.show', $e) }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-base-200 transition-colors">
                        <x-heroicon-o-building-office-2 class="w-5 h-5 flex-shrink-0 text-base-content/30" />
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $e->nombre }}</p>
                            <p class="text-xs text-base-content/50">
                                RFC: {{ $e->rfc ?: '—' }} · {{ $e->razon_social ?: '—' }}
                            </p>
                        </div>
                        <x-heroicon-o-chevron-right class="w-4 h-4 text-base-content/30 flex-shrink-0" />
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Órdenes de compra --}}
            @if(isset($ordenes) && $ordenes->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-base-content/40 mb-2 px-1">
                    Órdenes de compra ({{ $ordenes->count() }})
                </h3>
                <div class="card bg-base-100 shadow divide-y divide-base-200">
                    @foreach($ordenes as $o)
                    <a href="{{ route('admin.ordenes.show', $o) }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-base-200 transition-colors">
                        <x-heroicon-o-shopping-cart class="w-5 h-5 flex-shrink-0 text-base-content/30" />
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">Orden #{{ $o->ID_ORDEN }}</p>
                            <p class="text-xs text-base-content/50">
                                Folio: {{ $o->FOLIO_FACTURA ?: '—' }} ·
                                {{ Carbon\Carbon::parse($o->FECHA_REGISTRO)->format('d/m/Y') }} ·
                                ${{ number_format($o->IMPORTE_FACTURA, 2) }}
                            </p>
                        </div>
                        <x-heroicon-o-chevron-right class="w-4 h-4 text-base-content/30 flex-shrink-0" />
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        @endif
    </div>
</x-app-layout>