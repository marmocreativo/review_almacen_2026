<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.articulos.index') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
                <h2 class="text-xl font-semibold">Detalle del inventario</h2>
            </div>
            <a href="{{ route('admin.articulos.edit', $articulo) }}" class="btn btn-primary btn-sm">
                Editar
            </a>
        </div>
    </x-slot>

    <x-alert />

    {{-- Info principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Datos del artículo --}}
        <div class="card bg-base-100 shadow lg:col-span-2">
            <div class="card-body">
                <h3 class="card-title text-base mb-4">Información del ítem</h3>
                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">ID Item</p>
                        <p class="font-mono font-semibold text-lg">{{ $articulo->FOLIO }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Folio</p>
                        <p class="font-mono">{{ $articulo->SERIE ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Folio (núm.)</p>
                        <p class="font-mono">{{ $articulo->SERIE_NUMERICO ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Tipo</p>
                        <span class="badge {{ $articulo->TIPO === 'fisico' ? 'badge-info' : 'badge-accent' }}">
                            {{ ucfirst($articulo->TIPO) }}
                        </span>
                    </div>
                    <div class="col-span-2">
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Nombre</p>
                        <p class="font-medium">{{ $articulo->NOMBRE }}</p>
                    </div>
                    @if($articulo->DESCRIPCION)
                    <div class="col-span-2">
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Descripción</p>
                        <p>{{ $articulo->DESCRIPCION }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Formato</p>
                        <p>{{ $articulo->FORMATO ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Ubicación</p>
                        <p>{{ $articulo->UBICACION_UNICA ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Costo unitario</p>
                        <p class="font-mono">${{ number_format($articulo->COSTO_UNITARIO, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/50 text-xs uppercase tracking-wide">Precio venta</p>
                        <p class="font-mono">${{ number_format($articulo->PRECIO_VENTA, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cantidades --}}
        <div class="flex flex-col gap-4">
            @foreach([
                ['label'=>'En almacén',   'value'=>$articulo->CANTIDAD_ALMACEN,      'color'=>'text-success'],
                ['label'=>'En solicitudes','value'=>$articulo->CANTIDAD_SOLICITUDES,  'color'=>'text-warning'],
                ['label'=>'Destrucción',  'value'=>$articulo->CANTIDAD_DESTRUCCION,  'color'=>'text-error'],
                ['label'=>'Perdidos',     'value'=>$articulo->CANTIDAD_PERDIDOS,     'color'=>'text-base-content/40'],
            ] as $stat)
            <div class="card bg-base-100 shadow">
                <div class="card-body py-4">
                    <p class="text-xs text-base-content/50 uppercase tracking-wide">{{ $stat['label'] }}</p>
                    <p class="text-3xl font-bold font-mono {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Solicitudes --}}
    @if($articulo->solicitudesArticulos->isNotEmpty())
    <div class="card bg-base-100 shadow mb-4">
        <div class="card-body">
            <h3 class="card-title text-base mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Solicitudes ({{ $articulo->solicitudesArticulos->count() }})
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm table-zebra">
                    <thead>
                        <tr>
                            <th>Solicitud</th>
                            <th>Empresa</th>
                            <th>Sede</th>
                            <th>Estado</th>
                            <th class="text-center">Enviados</th>
                            <th class="text-center">Retornados</th>
                            <th class="text-center">Destrucción</th>
                            <th class="text-center">Perdidos</th>
                            <th>Fecha retorno</th>
                            <th>Candidato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articulo->solicitudesArticulos as $sa)
                        <tr>
                            <td>
                                <a href="{{ route('admin.solicitudes.show', $sa->ID_SOLICITUD) }}"
                                    class="link link-primary font-mono text-xs">
                                    #{{ $sa->ID_SOLICITUD }}
                                </a>
                            </td>
                            <td>{{ $sa->solicitud?->empresa?->nombre ?? '—' }}</td>
                            <td>{{ $sa->solicitud?->sede?->nombre ?? '—' }}</td>
                            <td>
                                <span class="badge badge-sm
                                    {{ $sa->ESTADO === 'retornado' ? 'badge-success' :
                                       ($sa->ESTADO === 'enviado'  ? 'badge-warning' : 'badge-ghost') }}">
                                    {{ ucfirst($sa->ESTADO) }}
                                </span>
                            </td>
                            <td class="text-center font-mono">{{ $sa->CANTIDAD_ENVIADA }}</td>
                            <td class="text-center font-mono">{{ $sa->CANTIDAD_A_ALMACEN }}</td>
                            <td class="text-center font-mono">{{ $sa->CANTIDAD_A_DESTRUCCION }}</td>
                            <td class="text-center font-mono">{{ $sa->CANTIDAD_PERDIDOS }}</td>
                            <td class="text-xs">{{ $sa->FECHA_RETORNO?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-xs">{{ $sa->NOMBRE_CANDIDATO ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Destrucción --}}
    @if($destruccion->isNotEmpty())
    <div class="card bg-base-100 shadow mb-4">
        <div class="card-body">
            <h3 class="card-title text-base mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Cajas de destrucción ({{ $destruccion->count() }})
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm table-zebra">
                    <thead>
                        <tr>
                            <th>Caja / Ubicación</th>
                            <th>Solicitud</th>
                            <th>Empresa</th>
                            <th class="text-center">Cantidad</th>
                            <th>Fecha retorno</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($destruccion as $d)
                        <tr>
                            <td class="font-mono text-xs">{{ $d->UBICACION_DESTRUCCION ?: '—' }}</td>
                            <td>
                                <a href="{{ route('admin.solicitudes.show', $d->ID_SOLICITUD) }}"
                                    class="link link-primary font-mono text-xs">
                                    #{{ $d->ID_SOLICITUD }}
                                </a>
                            </td>
                            <td>{{ $d->solicitud?->empresa?->nombre ?? '—' }}</td>
                            <td class="text-center font-mono">{{ $d->CANTIDAD_A_DESTRUCCION }}</td>
                            <td class="text-xs">{{ $d->FECHA_RETORNO?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Órdenes de compra --}}
    @if($articulo->ordenesArticulos->isNotEmpty())
    <div class="card bg-base-100 shadow mb-4">
        <div class="card-body">
            <h3 class="card-title text-base mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Órdenes de compra ({{ $articulo->ordenesArticulos->count() }})
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm table-zebra">
                    <thead>
                        <tr>
                            <th>ID Orden</th>
                            <th>Folio factura</th>
                            <th>Fecha registro</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Costo unitario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articulo->ordenesArticulos as $oa)
                        <tr>
                            <td>
                                <a href="{{ route('admin.ordenes.show', $oa->orden) }}"
                                    class="link link-primary font-mono text-xs">
                                    {{ $oa->ID_ORDEN }}
                                </a>
                            </td>
                            <td class="text-xs">{{ $oa->orden?->FOLIO_FACTURA ?: '—' }}</td>
                            <td class="text-xs">{{ $oa->orden?->FECHA_REGISTRO?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-center font-mono">{{ $oa->CANTIDAD }}</td>
                            <td class="text-right font-mono">${{ number_format($oa->COSTO_UNITARIO, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Sin relaciones --}}
    @if($articulo->solicitudesArticulos->isEmpty() && $destruccion->isEmpty() && $articulo->ordenesArticulos->isEmpty())
    <div class="alert alert-info">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Este ítem del inventario no está relacionado con ninguna solicitud, caja de destrucción ni orden de compra.</span>
    </div>
    @endif

</x-app-layout>