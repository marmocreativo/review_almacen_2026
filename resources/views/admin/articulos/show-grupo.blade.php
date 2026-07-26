<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div>
                <h2 class="text-xl font-semibold">Grupo de artículos</h2>
                <p class="text-sm text-base-content/60">
                    ID Item <span class="font-mono">{{ $primero->FOLIO }}</span> —
                    {{ $primero->SERIE === $ultimo->SERIE ? $primero->SERIE : $primero->SERIE . ' – ' . $ultimo->SERIE }}
                </p>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-ghost btn-sm gap-1">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                Volver
            </a>
        </div>
    </x-slot>

    <x-alert />

    {{-- RESUMEN --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <div class="card bg-base-100 shadow">
            <div class="card-body p-4">
                <span class="text-xs text-base-content/50">Artículos en el grupo</span>
                <span class="text-2xl font-bold">{{ $articulos->count() }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body p-4">
                <span class="text-xs text-base-content/50">En almacén</span>
                <span class="text-2xl font-bold">{{ $articulos->sum('CANTIDAD_ALMACEN') }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body p-4">
                <span class="text-xs text-base-content/50">En solicitudes</span>
                <span class="text-2xl font-bold">{{ $articulos->sum('CANTIDAD_SOLICITUDES') }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body p-4">
                <span class="text-xs text-base-content/50">Perdidos</span>
                <span class="text-2xl font-bold {{ $articulos->sum('CANTIDAD_PERDIDOS') > 0 ? 'text-error' : '' }}">
                    {{ $articulos->sum('CANTIDAD_PERDIDOS') }}
                </span>
            </div>
        </div>
    </div>

    {{-- DATOS GENERALES DEL GRUPO --}}
    <div class="card bg-base-100 shadow mb-4">
        <div class="card-body">
            <h3 class="font-semibold mb-3">Datos generales</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-base-content/50 block text-xs">Nombre</span>
                    {{ $primero->NOMBRE }}
                </div>
                <div>
                    <span class="text-base-content/50 block text-xs">Formato</span>
                    {{ $primero->FORMATO ?: '—' }}
                </div>
                <div>
                    <span class="text-base-content/50 block text-xs">Tipo</span>
                    <span class="badge badge-sm {{ $primero->TIPO === 'fisico' ? 'badge-info' : 'badge-accent' }}">
                        {{ ucfirst($primero->TIPO) }}
                    </span>
                </div>
                <div>
                    <span class="text-base-content/50 block text-xs">Tipo de examen</span>
                    {{ $primero->tipoExamen?->nombre ?? '—' }}
                </div>
                <div>
                    <span class="text-base-content/50 block text-xs">Costo unitario</span>
                    ${{ number_format($primero->COSTO_UNITARIO, 2) }}
                </div>
                <div>
                    <span class="text-base-content/50 block text-xs">Precio venta</span>
                    ${{ number_format($primero->PRECIO_VENTA, 2) }}
                </div>
            </div>
        </div>
    </div>

    {{-- LISTADO DE ARTÍCULOS DEL GRUPO --}}
    <div class="card bg-base-100 shadow mb-4">
        <div class="card-body p-0">
            <h3 class="font-semibold px-4 pt-4">Artículos individuales ({{ $articulos->count() }})</h3>

            {{-- Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Almacén</th>
                            <th>Solicitudes</th>
                            <th>Destrucción</th>
                            <th>Perdidos</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articulos as $a)
                            @php $b = \App\Models\Articulo::estadoBadge($a->CANTIDAD_ALMACEN, $a->CANTIDAD_SOLICITUDES, $a->CANTIDAD_DESTRUCCION, $a->CANTIDAD_PERDIDOS); @endphp
                            <tr>
                                <td class="font-mono text-xs">{{ $a->SERIE ?: '—' }}</td>
                                <td class="font-mono">{{ $a->CANTIDAD_ALMACEN }}</td>
                                <td>{{ $a->CANTIDAD_SOLICITUDES }}</td>
                                <td>{{ $a->CANTIDAD_DESTRUCCION }}</td>
                                <td class="{{ $a->CANTIDAD_PERDIDOS > 0 ? 'text-error font-semibold' : '' }}">{{ $a->CANTIDAD_PERDIDOS }}</td>
                                <td><span class="badge badge-sm {{ $b['class'] }}">{{ $b['label'] }}</span></td>
                                <td>
                                    <a href="{{ route('admin.articulos.show', $a) }}" class="btn btn-ghost btn-xs">Ver individual</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="md:hidden divide-y divide-base-200">
                @foreach($articulos as $a)
                    @php $b = \App\Models\Articulo::estadoBadge($a->CANTIDAD_ALMACEN, $a->CANTIDAD_SOLICITUDES, $a->CANTIDAD_DESTRUCCION, $a->CANTIDAD_PERDIDOS); @endphp
                    <div class="p-4 flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm">{{ $a->SERIE ?: '—' }}</span>
                            <span class="badge badge-sm {{ $b['class'] }}">{{ $b['label'] }}</span>
                        </div>
                        <div class="text-xs text-base-content/60 flex gap-3 flex-wrap">
                            <span>Almacén: <strong>{{ $a->CANTIDAD_ALMACEN }}</strong></span>
                            <span>Solicitudes: <strong>{{ $a->CANTIDAD_SOLICITUDES }}</strong></span>
                            <span>Destrucción: <strong>{{ $a->CANTIDAD_DESTRUCCION }}</strong></span>
                            <span class="{{ $a->CANTIDAD_PERDIDOS > 0 ? 'text-error' : '' }}">Perdidos: <strong>{{ $a->CANTIDAD_PERDIDOS }}</strong></span>
                        </div>
                        <a href="{{ route('admin.articulos.show', $a) }}" class="link link-primary text-xs mt-1">Ver individual →</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SOLICITUDES RELACIONADAS --}}
    @php
        $solicitudesRelacionadas = $articulos->flatMap->solicitudesArticulos->unique('ID_SOLICITUD');
    @endphp
    @if($solicitudesRelacionadas->isNotEmpty())
        <div class="card bg-base-100 shadow mb-4">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Solicitudes relacionadas</h3>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Solicitud</th>
                                <th>Empresa</th>
                                <th>Sede</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudesRelacionadas as $sa)
                                <tr>
                                    <td class="font-mono">#{{ $sa->solicitud?->ID_SOLICITUD }}</td>
                                    <td>{{ $sa->solicitud?->empresa?->nombre ?? '—' }}</td>
                                    <td>{{ $sa->solicitud?->sede?->nombre ?? '—' }}</td>
                                    <td>
                                        @if($sa->solicitud)
                                            <a href="{{ route('admin.solicitudes.show', $sa->solicitud) }}" class="btn btn-ghost btn-xs">Ver</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- DESTRUCCIÓN RELACIONADA --}}
    @if($destruccion->isNotEmpty())
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Cajas de destrucción relacionadas</h3>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Cantidad</th>
                                <th>Solicitud</th>
                                <th>Empresa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($destruccion as $d)
                                <tr>
                                    <td class="font-mono text-xs">{{ $d->SERIE ?: '—' }}</td>
                                    <td>{{ $d->CANTIDAD_A_DESTRUCCION }}</td>
                                    <td class="font-mono">#{{ $d->ID_SOLICITUD }}</td>
                                    <td>{{ $d->solicitud?->empresa?->nombre ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>