<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold">Inventario</h2>
                <div class="join">
                    <a href="{{ request()->fullUrlWithQuery(['vista' => null, 'page' => 1]) }}"
                        class="btn btn-xs join-item {{ !$vistaDetalle ? 'btn-primary' : 'btn-ghost' }}">
                        Agrupado
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['vista' => 'detalle', 'page' => 1]) }}"
                        class="btn btn-xs join-item {{ $vistaDetalle ? 'btn-primary' : 'btn-ghost' }}">
                        Detallado
                    </a>
                </div>
            </div>
            <div class="flex gap-2 flex-wrap justify-end">
                <a href="{{ route('admin.articulos.exportar') }}" class="btn btn-success btn-sm">
                    ↓ Excel
                </a>
                <button onclick="document.getElementById('modal-individual').showModal()"
                    class="btn btn-primary btn-sm">
                    + Alta individual
                </button>
                <button onclick="document.getElementById('modal-rango').showModal()"
                    class="btn btn-secondary btn-sm">
                    + Alta por rango
                </button>
            </div>
        </div>
    </x-slot>

    <x-alert />

    {{-- BÚSQUEDA --}}
    <div x-data="{ avanzada: {{ request()->hasAny(['folio','serie','tipo','formato']) && !request()->filled('q') ? 'true' : 'false' }} }" class="mb-4">

        {{-- Búsqueda simple --}}
        <form method="GET" x-show="!avanzada" class="card bg-base-100 shadow">
            <div class="card-body py-3">
                {{-- Fila 1: input + buscar --}}
                <div class="flex gap-2 items-center">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Buscar por ID Item o folio..."
                        class="input input-bordered flex-1" />
                    @if(request('orden_campo'))
                        <input type="hidden" name="orden_campo" value="{{ request('orden_campo') }}">
                        <input type="hidden" name="orden_dir" value="{{ request('orden_dir') }}">
                    @endif
                    <button type="submit" class="btn btn-primary">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        <span class="hidden sm:inline ml-1">Buscar</span>
                    </button>
                </div>
                {{-- Fila 2: acciones secundarias --}}
                <div class="flex gap-2 items-center mt-1">
                    @if(request()->filled('q'))
                        <a href="{{ route('admin.articulos.index') }}" class="btn btn-ghost btn-sm gap-1">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Limpiar
                        </a>
                    @endif
                    <button type="button" @click="avanzada = true" class="btn btn-ghost btn-sm gap-1">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
                        Búsqueda avanzada
                    </button>
                </div>
            </div>
        </form>

        {{-- Búsqueda avanzada --}}
        <form method="GET" x-show="avanzada" class="card bg-base-100 shadow">
            <div class="card-body py-3">
                {{-- Grid de filtros --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:flex lg:flex-wrap gap-3 items-end">
                    <div class="form-control col-span-1">
                        <label class="label py-0"><span class="label-text text-xs">ID Item</span></label>
                        <input type="text" name="folio" value="{{ request('folio') }}"
                            placeholder="Folio..." class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control col-span-1">
                        <label class="label py-0"><span class="label-text text-xs">Folio</span></label>
                        <input type="text" name="serie" value="{{ request('serie') }}"
                            placeholder="Serie..." class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control col-span-1">
                        <label class="label py-0"><span class="label-text text-xs">Tipo</span></label>
                        <select name="tipo" class="select select-bordered select-sm w-full">
                            <option value="">Todos</option>
                            <option value="fisico"   {{ request('tipo') === 'fisico'   ? 'selected' : '' }}>Físico</option>
                            <option value="digital"  {{ request('tipo') === 'digital'  ? 'selected' : '' }}>Digital</option>
                        </select>
                    </div>
                    <div class="form-control col-span-1">
                        <label class="label py-0"><span class="label-text text-xs">Formato</span></label>
                        <input type="text" name="formato" value="{{ request('formato') }}"
                            placeholder="Formato..." class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control col-span-1">
                        <label class="label py-0"><span class="label-text text-xs">Ordenar por</span></label>
                        <select name="orden_campo" class="select select-bordered select-sm w-full">
                            <option value="FOLIO"            {{ request('orden_campo','FOLIO') === 'FOLIO'            ? 'selected' : '' }}>ID Item</option>
                            <option value="SERIE"            {{ request('orden_campo') === 'SERIE'            ? 'selected' : '' }}>Folio</option>
                            <option value="FORMATO"          {{ request('orden_campo') === 'FORMATO'          ? 'selected' : '' }}>Formato</option>
                            <option value="CANTIDAD_ALMACEN" {{ request('orden_campo') === 'CANTIDAD_ALMACEN' ? 'selected' : '' }}>Cant. almacén</option>
                        </select>
                    </div>
                    <div class="form-control col-span-1">
                        <label class="label py-0"><span class="label-text text-xs">Dirección</span></label>
                        <select name="orden_dir" class="select select-bordered select-sm w-full">
                            <option value="asc"  {{ request('orden_dir','asc') === 'asc'  ? 'selected' : '' }}>↑ Asc</option>
                            <option value="desc" {{ request('orden_dir') === 'desc' ? 'selected' : '' }}>↓ Desc</option>
                        </select>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-base-200">
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-funnel class="w-4 h-4" />
                        Filtrar
                    </button>
                    <a href="{{ route('admin.articulos.index') }}" class="btn btn-ghost btn-sm gap-1">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                        Limpiar
                    </a>
                    <button type="button" @click="avanzada = false" class="btn btn-ghost btn-sm gap-1 ml-auto">
                        <x-heroicon-o-chevron-left class="w-4 h-4" />
                        Búsqueda simple
                    </button>
                </div>
            </div>
        </form>

        {{-- Orden rápido (visible en búsqueda simple) --}}
        <div x-show="!avanzada" class="flex gap-2 mt-2 items-center text-sm text-base-content/60 overflow-x-auto pb-1">
            <span>Ordenar:</span>
            @foreach(['FOLIO'=>'ID Item','SERIE'=>'Folio','FORMATO'=>'Formato','CANTIDAD_ALMACEN'=>'Almacén'] as $campo => $label)
                @php
                    $activo = request('orden_campo', 'FOLIO') === $campo;
                    $dir = ($activo && request('orden_dir','asc') === 'asc') ? 'desc' : 'asc';
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['orden_campo'=>$campo,'orden_dir'=>$dir,'page'=>1]) }}"
                    class="btn btn-xs {{ $activo ? 'btn-primary' : 'btn-ghost' }}">
                    {{ $label }}
                    @if($activo)
                        {{ request('orden_dir','asc') === 'asc' ? '↑' : '↓' }}
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    @if($vistaDetalle)
    {{-- Tabla detallada: folios individuales sin agrupar --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            {{-- VISTA DESKTOP: tabla --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="bg-base-200">
                            <th>ID Item</th>
                            <th>Folio</th>
                            <th>Nombre</th>
                            <th>Formato</th>
                            <th>Tipo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginator as $articulo)
                            @php
                                $estadoItem = \App\Models\Articulo::estadoBadge($articulo->CANTIDAD_ALMACEN, $articulo->CANTIDAD_SOLICITUDES, $articulo->CANTIDAD_DESTRUCCION, $articulo->CANTIDAD_PERDIDOS);
                                $deletable = $articulo->esDeletable();
                            @endphp
                            <tr class="hover">
                                <td class="font-mono font-bold text-primary">{{ $articulo->FOLIO }}</td>
                                <td class="font-mono text-xs">{{ $articulo->SERIE ?: '—' }}</td>
                                <td>{{ $articulo->NOMBRE }}</td>
                                <td>{{ $articulo->FORMATO ?: '—' }}</td>
                                <td>
                                    <span class="badge badge-sm {{ $articulo->TIPO === 'fisico' ? 'badge-info' : 'badge-accent' }}">
                                        {{ $articulo->TIPO }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm {{ $estadoItem['class'] }}">{{ $estadoItem['label'] }}</span>
                                    <button type="button"
                                        onclick="verDetalleCantidades('{{ $articulo->FOLIO }}', {{ $articulo->CANTIDAD_ALMACEN }}, {{ $articulo->CANTIDAD_SOLICITUDES }}, {{ $articulo->CANTIDAD_DESTRUCCION }}, {{ $articulo->CANTIDAD_PERDIDOS }})"
                                        class="btn btn-xs btn-circle btn-ghost align-middle" title="Ver detalle">
                                        <x-heroicon-o-question-mark-circle class="w-4 h-4" />
                                    </button>
                                </td>
                                <td class="text-center">
                                    <div class="flex gap-1 justify-center">
                                        <a href="{{ route('admin.articulos.show', $articulo) }}"
                                            class="btn btn-xs btn-outline" title="Ver">
                                            <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                        </a>
                                        <a href="{{ route('admin.articulos.edit', $articulo) }}"
                                            class="btn btn-xs btn-info" title="Editar">
                                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                        </a>
                                        @if($deletable)
                                            <button
                                                onclick="confirmarEliminar({{ $articulo->ID_ARTICULO }})"
                                                class="btn btn-xs btn-error" title="Eliminar">
                                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-base-content/50 py-8">
                                    No hay artículos en el inventario.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL: cards --}}
            <div class="md:hidden divide-y divide-base-200">
                @forelse($paginator as $articulo)
                    @php
                        $estadoItem = \App\Models\Articulo::estadoBadge($articulo->CANTIDAD_ALMACEN, $articulo->CANTIDAD_SOLICITUDES, $articulo->CANTIDAD_DESTRUCCION, $articulo->CANTIDAD_PERDIDOS);
                        $deletable = $articulo->esDeletable();
                    @endphp
                    <div class="p-3">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-primary text-sm">{{ $articulo->FOLIO }}</span>
                                    <span class="text-xs text-base-content/40">{{ $articulo->TIPO === 'fisico' ? 'Físico' : 'Digital' }}</span>
                                </div>
                                <p class="text-sm font-medium mt-0.5 truncate">{{ $articulo->NOMBRE }}</p>
                                <p class="text-xs text-base-content/50 font-mono">{{ $articulo->SERIE ?: 'Sin folio' }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <div class="flex items-center gap-1">
                                    <span class="badge badge-xs {{ $estadoItem['class'] }}">{{ $estadoItem['label'] }}</span>
                                    <button type="button"
                                        onclick="verDetalleCantidades('{{ $articulo->FOLIO }}', {{ $articulo->CANTIDAD_ALMACEN }}, {{ $articulo->CANTIDAD_SOLICITUDES }}, {{ $articulo->CANTIDAD_DESTRUCCION }}, {{ $articulo->CANTIDAD_PERDIDOS }})"
                                        class="btn btn-xs btn-circle btn-ghost">
                                        <x-heroicon-o-question-mark-circle class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                                <div class="flex gap-1">
                                    <a href="{{ route('admin.articulos.show', $articulo) }}" class="btn btn-xs btn-outline">
                                        <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                    </a>
                                    <a href="{{ route('admin.articulos.edit', $articulo) }}" class="btn btn-xs btn-info">
                                        <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                    </a>
                                    @if($deletable)
                                        <button onclick="confirmarEliminar({{ $articulo->ID_ARTICULO }})" class="btn btn-xs btn-error">
                                            <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay artículos en el inventario.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @else
    {{-- Tabla agrupada por folio --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            {{-- VISTA DESKTOP: tabla --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="w-8"></th>
                            <th>ID Item</th>
                            <th>Nombre</th>
                            <th>Folios / Cantidad</th>
                            <th>Formato</th>
                            <th>Tipo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginator as $grupo)
                            @php $gid = Str::slug($grupo['folio']); @endphp
                            <tr class="bg-base-100 hover cursor-pointer font-medium"
                                onclick="toggleGrupo('{{ $gid }}')">
                                <td>
                                    <x-heroicon-o-chevron-right
                                        id="icon-{{ $gid }}"
                                        class="w-4 h-4 transition-transform duration-200 text-base-content/40" />
                                </td>
                                <td class="font-mono font-bold text-primary">{{ $grupo['folio'] }}</td>
                                <td>{{ $grupo['nombre'] }}</td>
                                <td>
                                    @if($grupo['tiene_serie'])
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($grupo['rangos'] as $rango)
                                                <span class="badge badge-outline badge-sm font-mono">{{ $rango }}</span>
                                            @endforeach
                                        </div>
                                        <span class="text-xs text-base-content/50 mt-0.5 block">
                                            {{ $grupo['total'] }} {{ $grupo['total'] === 1 ? 'pieza' : 'piezas' }}
                                        </span>
                                    @else
                                        <span class="text-base-content/50 text-sm italic">A granel</span>
                                    @endif
                                </td>
                                <td>{{ $grupo['formato'] ?: '—' }}</td>
                                <td>
                                    <span class="badge badge-sm {{ $grupo['tipo'] === 'fisico' ? 'badge-info' : 'badge-accent' }}">
                                        {{ $grupo['tipo'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm {{ $grupo['estado']['class'] }}">{{ $grupo['estado']['label'] }}</span>
                                    <button type="button"
                                        onclick="event.stopPropagation(); verDetalleCantidades('{{ $grupo['folio'] }}', {{ $grupo['cantidad_almacen'] }}, {{ $grupo['cantidad_solicitudes'] }}, {{ $grupo['cantidad_destruccion'] }}, {{ $grupo['cantidad_perdidos'] }})"
                                        class="btn btn-xs btn-circle btn-ghost align-middle" title="Ver detalle">
                                        <x-heroicon-o-question-mark-circle class="w-4 h-4" />
                                    </button>
                                </td>
                                <td class="text-center">
                                    @if(!$grupo['tiene_serie'])
                                        <button
                                            onclick="event.stopPropagation(); abrirAgregarAlmacen({{ $grupo['items']->first()->ID_ARTICULO }}, {{ $grupo['cantidad_almacen'] }})"
                                            class="btn btn-xs btn-ghost text-primary" title="Agregar al almacén">
                                            <x-heroicon-o-plus class="w-4 h-4" />
                                        </button>
                                    @endif
                                    @if(!empty($grupo['ids_deletables']))
                                        <button
                                            onclick="event.stopPropagation(); confirmarEliminarGrupo({{ json_encode($grupo['ids_deletables']) }}, '{{ $grupo['folio'] }}')"
                                            class="btn btn-xs btn-ghost text-error" title="Eliminar ID Item">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            @foreach($grupo['items'] as $articulo)
                                @php $deletable = $articulo->esDeletable(); @endphp
                                <tr class="grupo-{{ $gid }} hidden bg-base-200/40 text-sm">
                                    <td></td>
                                    <td class="pl-6 text-base-content/50 font-mono text-xs">└</td>
                                    <td class="text-base-content/70">{{ $articulo->NOMBRE }}</td>
                                    <td class="font-mono text-xs">{{ $articulo->SERIE ?: '—' }}</td>
                                    <td class="text-xs text-base-content/60">{{ $articulo->FORMATO ?: '—' }}</td>
                                    <td></td>
                                    <td class="text-center">
                                        @php $estadoItem = \App\Models\Articulo::estadoBadge($articulo->CANTIDAD_ALMACEN, $articulo->CANTIDAD_SOLICITUDES, $articulo->CANTIDAD_DESTRUCCION, $articulo->CANTIDAD_PERDIDOS); @endphp
                                        <span class="badge badge-xs {{ $estadoItem['class'] }}">{{ $estadoItem['label'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex gap-1 justify-center">
                                            <a href="{{ route('admin.articulos.show', $articulo) }}"
                                                class="btn btn-xs btn-outline" title="Ver">
                                                <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                            </a>
                                            <a href="{{ route('admin.articulos.edit', $articulo) }}"
                                                class="btn btn-xs btn-info" title="Editar">
                                                <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                            </a>
                                            @if($deletable)
                                                <button
                                                    onclick="confirmarEliminar({{ $articulo->ID_ARTICULO }})"
                                                    class="btn btn-xs btn-error" title="Eliminar">
                                                    <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-base-content/50 py-8">
                                    No hay artículos en el inventario.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL: cards --}}
            <div class="md:hidden divide-y divide-base-200">
                @forelse($paginator as $grupo)
                    @php $gid = Str::slug($grupo['folio']); @endphp
                    <div class="p-3 grupo-mobile-card">

                        {{-- Cabecera del grupo --}}
                        <div class="flex items-center justify-between gap-2 cursor-pointer"
                            onclick="toggleGrupoMobile('{{ $gid }}')">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-primary text-sm">{{ $grupo['folio'] }}</span>
                                    <span class="text-xs text-base-content/40 font-normal">
                                        {{ $grupo['tipo'] === 'fisico' ? 'Físico' : 'Digital' }}
                                    </span>
                                    @if($grupo['formato'])
                                        <span class="text-xs text-base-content/30">· {{ $grupo['formato'] }}</span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium mt-0.5 truncate">{{ $grupo['nombre'] }}</p>
                            </div>

                            {{-- Cantidad almacén siempre visible --}}
                            <div class="flex items-center gap-2 shrink-0">
                                <div class="text-right">
                                    <div class="font-mono font-bold text-lg leading-none">{{ $grupo['cantidad_almacen'] }}</div>
                                    <div class="text-xs text-base-content/40">almacén</div>
                                </div>
                                <x-heroicon-o-chevron-right
                                    id="icon-mobile-{{ $gid }}"
                                    class="w-4 h-4 transition-transform duration-200 text-base-content/30" />
                            </div>
                        </div>

                        {{-- Contenido desplegado --}}
                        <div id="grupo-mobile-{{ $gid }}" class="hidden mt-3">

                            {{-- Fila de stats + botón agregar --}}
                            <div class="flex items-center gap-3 text-xs bg-base-200/60 rounded-lg px-3 py-2 mb-3">
                                <span class="badge badge-sm {{ $grupo['estado']['class'] }}">{{ $grupo['estado']['label'] }}</span>
                                <button type="button"
                                    onclick="verDetalleCantidades('{{ $grupo['folio'] }}', {{ $grupo['cantidad_almacen'] }}, {{ $grupo['cantidad_solicitudes'] }}, {{ $grupo['cantidad_destruccion'] }}, {{ $grupo['cantidad_perdidos'] }})"
                                    class="btn btn-xs btn-circle btn-ghost" title="Ver detalle">
                                    <x-heroicon-o-question-mark-circle class="w-4 h-4" />
                                </button>
                                @if(!$grupo['tiene_serie'])
                                    <button
                                        onclick="abrirAgregarAlmacen({{ $grupo['items']->first()->ID_ARTICULO }}, {{ $grupo['cantidad_almacen'] }})"
                                        class="btn btn-xs btn-primary gap-1 ml-auto">
                                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                                    </button>
                                @endif
                            </div>

                            {{-- Series / items --}}
                            <div class="space-y-2">
                                @foreach($grupo['items'] as $articulo)
                                    @php $deletable = $articulo->esDeletable(); @endphp
                                    <div class="bg-base-200/50 rounded-lg p-2 flex items-center justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-mono text-xs font-medium truncate">
                                                {{ $articulo->SERIE ?: 'Sin folio' }}
                                            </p>
                                            <p class="text-xs text-base-content/50">
                                                Almacén: {{ $articulo->CANTIDAD_ALMACEN }}
                                            </p>
                                        </div>
                                        <div class="flex gap-1 shrink-0">
                                            <a href="{{ route('admin.articulos.show', $articulo) }}"
                                                class="btn btn-xs btn-outline" title="Ver">
                                                <x-heroicon-o-eye class="w-4 h-4" />
                                            </a>
                                            <a href="{{ route('admin.articulos.edit', $articulo) }}"
                                                class="btn btn-xs btn-info" title="Editar">
                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                            </a>
                                            @if($deletable)
                                                <button
                                                    onclick="confirmarEliminar({{ $articulo->ID_ARTICULO }})"
                                                    class="btn btn-xs btn-error" title="Eliminar">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Botón eliminar grupo --}}
                            @if(!empty($grupo['ids_deletables']))
                                <div class="mt-2 flex justify-end">
                                    <button
                                        onclick="confirmarEliminarGrupo({{ json_encode($grupo['ids_deletables']) }}, '{{ $grupo['folio'] }}')"
                                        class="btn btn-xs btn-error gap-1">
                                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                        Eliminar ID Item
                                    </button>
                                </div>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay artículos en el inventario.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
    @endif

    <div class="mt-4">{{ $paginator->links() }}</div>


    {{-- ===================== MODAL ALTA INDIVIDUAL ===================== --}}
    <dialog id="modal-individual" class="modal">
        <div class="modal-box w-11/12 max-w-lg">
            <h3 class="font-bold text-lg mb-4">Alta individual</h3>

            <div class="grid grid-cols-2 gap-3">
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">ID Item</span></label>
                    <input type="text" id="i-folio" class="input input-bordered input-sm font-mono w-full" placeholder="Auto si se deja vacío" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Folio</span></label>
                    <input type="text" id="i-serie" class="input input-bordered input-sm font-mono w-full" placeholder="Ej: S451232154" />
                </div>
                <div class="form-control col-span-2">
                    <label class="label py-1"><span class="label-text font-medium">Nombre *</span></label>
                    <input type="text" id="i-nombre" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Formato</span></label>
                    <input type="text" id="i-formato" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Tipo *</span></label>
                    <select id="i-tipo" class="select select-bordered select-sm w-full">
                        <option value="fisico">Físico</option>
                        <option value="digital">Digital</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Costo unitario</span></label>
                    <input type="number" step="0.01" id="i-costo" value="0" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Precio venta</span></label>
                    <input type="number" step="0.01" id="i-precio" value="0" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control col-span-2" id="i-cantidad-wrap">
                    <label class="label py-1"><span class="label-text font-medium">Cantidad en almacén</span></label>
                    <input type="number" id="i-cantidad" value="1" min="1" class="input input-bordered input-sm w-full" />
                    <p class="text-xs text-base-content/40 mt-1">Solo aplica si no capturas un folio (artículo a granel).</p>
                </div>
            </div>

            <div id="individual-error" class="alert alert-error text-sm mt-3 hidden"></div>

            <div class="modal-action mt-4">
                <button onclick="guardarIndividual()" id="btn-guardar-individual" class="btn btn-primary">Guardar</button>
                <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- ===================== MODAL ALTA POR RANGO ===================== --}}
    <dialog id="modal-rango" class="modal">
        <div class="modal-box w-11/12 max-w-lg">
            <h3 class="font-bold text-lg mb-4">Alta por rango</h3>

            <div class="grid grid-cols-2 gap-3">
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Folio inicial *</span></label>
                    <input type="text" id="r-serie-inicial" class="input input-bordered input-sm font-mono w-full" placeholder="S451232154" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Folio final *</span></label>
                    <input type="text" id="r-serie-final" class="input input-bordered input-sm font-mono w-full" placeholder="S451232200" />
                </div>
                <div class="form-control col-span-2">
                    <label class="label py-1"><span class="label-text font-medium">ID Item</span></label>
                    <input type="text" id="r-folio" class="input input-bordered input-sm font-mono w-full" placeholder="Auto si se deja vacío" />
                </div>
                <div class="form-control col-span-2">
                    <label class="label py-1"><span class="label-text font-medium">Nombre *</span></label>
                    <input type="text" id="r-nombre" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Formato</span></label>
                    <input type="text" id="r-formato" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Tipo *</span></label>
                    <select id="r-tipo" class="select select-bordered select-sm w-full">
                        <option value="fisico">Físico</option>
                        <option value="digital">Digital</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Costo unitario</span></label>
                    <input type="number" step="0.01" id="r-costo" value="0" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Precio venta</span></label>
                    <input type="number" step="0.01" id="r-precio" value="0" class="input input-bordered input-sm w-full" />
                </div>
            </div>

            <div id="preview-rango" class="mt-3 p-2 rounded bg-base-200 text-sm font-mono text-base-content/70 min-h-8"></div>
            <div id="rango-error" class="alert alert-error text-sm mt-3 hidden"></div>

            <div class="modal-action mt-4">
                <button onclick="guardarRango()" id="btn-guardar-rango" class="btn btn-primary">Guardar</button>
                <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- ===================== MODAL INFORME ===================== --}}
    <dialog id="modal-informe" class="modal">
        <div class="modal-box w-11/12 max-w-lg">
            <h3 class="font-bold text-lg mb-4">Resultado de la alta</h3>
            <div id="informe-contenido" class="space-y-3 text-sm"></div>
            <div class="modal-action mt-4">
                <button onclick="location.reload()" class="btn btn-primary">Aceptar</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- ===================== MODAL AGREGAR ALMACÉN ===================== --}}
    <dialog id="modal-agregar-almacen" class="modal">
        <div class="modal-box max-w-sm">
            <h3 class="font-bold text-lg mb-1">Agregar al almacén</h3>
            <p class="text-sm text-base-content/60 mb-4">
                Cantidad actual: <strong id="almacen-actual">0</strong>
            </p>
            <div class="form-control">
                <label class="label"><span class="label-text">Cantidad a agregar</span></label>
                <input type="number" id="input-agregar-cantidad" min="1" value="1"
                    class="input input-bordered" />
            </div>
            <div id="agregar-error" class="text-error text-sm mt-2 hidden"></div>
            <div class="modal-action">
                <button onclick="guardarAgregarAlmacen()" class="btn btn-primary">Agregar</button>
                <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- ===================== MODAL CONFIRMAR ELIMINAR ===================== --}}
    <dialog id="modal-eliminar" class="modal">
        <div class="modal-box max-w-sm">
            <h3 class="font-bold text-lg text-error mb-2">¿Eliminar del inventario?</h3>
            <p class="text-sm text-base-content/70 mb-4">Esta acción no se puede deshacer.</p>
            <div class="modal-action">
                <button onclick="ejecutarEliminar()" class="btn btn-error">Sí, eliminar</button>
                <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- ===================== MODAL DETALLE DE CANTIDADES ===================== --}}
    <dialog id="modal-detalle-cantidades" class="modal">
        <div class="modal-box max-w-sm">
            <h3 class="font-bold text-lg mb-1">Detalle de existencias</h3>
            <p class="text-sm text-base-content/50 mb-4 font-mono" id="detalle-folio"></p>
            <div class="grid grid-cols-2 gap-3">
                <div class="text-center bg-base-200 rounded-lg p-3">
                    <p class="text-xs text-base-content/50">Almacén</p>
                    <p class="text-xl font-bold font-mono text-success" id="detalle-almacen">0</p>
                </div>
                <div class="text-center bg-base-200 rounded-lg p-3">
                    <p class="text-xs text-base-content/50">Solicitudes</p>
                    <p class="text-xl font-bold font-mono text-info" id="detalle-solicitudes">0</p>
                </div>
                <div class="text-center bg-base-200 rounded-lg p-3">
                    <p class="text-xs text-base-content/50">Destrucción</p>
                    <p class="text-xl font-bold font-mono text-warning" id="detalle-destruccion">0</p>
                </div>
                <div class="text-center bg-base-200 rounded-lg p-3">
                    <p class="text-xs text-base-content/50">Perdidos</p>
                    <p class="text-xl font-bold font-mono text-error" id="detalle-perdidos">0</p>
                </div>
            </div>
            <div class="modal-action">
                <form method="dialog"><button class="btn btn-ghost btn-sm">Cerrar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>


<script>
    const csrfToken = '{{ csrf_token() }}';
    const urlStore  = '{{ route('admin.articulos.store') }}';
    const urlRango  = '{{ route('admin.articulos.rango') }}';
    const urlBase   = '{{ url('admin/articulos') }}';

    let _articulo_id_eliminar = null;
    let _articulo_id_almacen  = null;

    // ==================== ALTA INDIVIDUAL ====================
    async function guardarIndividual() {
        const error = document.getElementById('individual-error');
        error.classList.add('hidden');

        const payload = {
            FOLIO:          document.getElementById('i-folio').value.trim(),
            SERIE:          document.getElementById('i-serie').value.trim(),
            NOMBRE:         document.getElementById('i-nombre').value.trim(),
            FORMATO:        document.getElementById('i-formato').value.trim(),
            TIPO:           document.getElementById('i-tipo').value,
            COSTO_UNITARIO: document.getElementById('i-costo').value,
            PRECIO_VENTA:   document.getElementById('i-precio').value,
            CANTIDAD_ALMACEN: parseInt(document.getElementById('i-cantidad').value) || 1,
        };

        const btn = document.getElementById('btn-guardar-individual');
        const textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="loading loading-spinner loading-xs"></span> Agregando a inventario...`;

        try {
            const res = await fetch(urlStore, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload),
            });
            const data = await res.json().catch(() => ({}));

            if (!res.ok || !data.success) {
                error.textContent = data.message ?? 'Error al guardar.';
                error.classList.remove('hidden');
                return;
            }

            document.getElementById('modal-individual').close();
            mostrarInforme({
                titulo: 'Artículo agregado',
                resumen: `Se agregó correctamente el artículo con ID Item <strong>${data.id_item}</strong>.`,
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        }
    }

    // ==================== ALTA POR RANGO ====================
    function actualizarPreviewRango() {
        const inicial = document.getElementById('r-serie-inicial').value.trim().toUpperCase();
        const final   = document.getElementById('r-serie-final').value.trim().toUpperCase();
        const preview = document.getElementById('preview-rango');
        const patron  = /^S(\d{9})(?:-\d+)?$/;
        const mInicial = inicial.match(patron);
        const mFinal   = final.match(patron);

        if (!mInicial || !mFinal) { preview.textContent = ''; return; }

        const nInicial = parseInt(mInicial[1]);
        const nFinal   = parseInt(mFinal[1]);
        if (nFinal < nInicial) { preview.textContent = 'El folio final debe ser mayor o igual al inicial.'; return; }

        const total = nFinal - nInicial + 1;
        preview.textContent = `Se generarán ${total} folios: S${mInicial[1]} → S${mFinal[1]}`;
    }
    ['r-serie-inicial', 'r-serie-final'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', actualizarPreviewRango);
    });

    async function guardarRango() {
        const error = document.getElementById('rango-error');
        error.classList.add('hidden');

        const payload = {
            FOLIO:          document.getElementById('r-folio').value.trim(),
            SERIE_INICIAL:  document.getElementById('r-serie-inicial').value.trim(),
            SERIE_FINAL:    document.getElementById('r-serie-final').value.trim(),
            NOMBRE:         document.getElementById('r-nombre').value.trim(),
            FORMATO:        document.getElementById('r-formato').value.trim(),
            TIPO:           document.getElementById('r-tipo').value,
            COSTO_UNITARIO: document.getElementById('r-costo').value,
            PRECIO_VENTA:   document.getElementById('r-precio').value,
        };

        const btn = document.getElementById('btn-guardar-rango');
        const textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="loading loading-spinner loading-xs"></span> Agregando a inventario...`;

        try {
            const res = await fetch(urlRango, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload),
            });
            const data = await res.json().catch(() => ({}));

            if (!res.ok || !data.success) {
                error.textContent = data.message ?? 'Error al guardar.';
                error.classList.remove('hidden');
                return;
            }

            document.getElementById('modal-rango').close();
            mostrarInforme({
                titulo: 'Alta por rango completada',
                resumen: `ID Item: <strong>${data.id_item}</strong>`,
                agregados: data.agregados,
                existentes: data.existentes,
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        }
    }

    // ==================== INFORME ====================
    function mostrarInforme({ resumen, agregados = null, existentes = null }) {
        const contenedor = document.getElementById('informe-contenido');
        let html = `<p>${resumen}</p>`;

        if (agregados) {
            html += `<div class="alert alert-success"><span>${agregados.length} folio(s) agregados correctamente.</span></div>`;
        }
        if (existentes && existentes.length) {
            html += `<div class="alert alert-warning"><span>${existentes.length} folio(s) no se agregaron porque ya existían: ${existentes.join(', ')}</span></div>`;
        }

        contenedor.innerHTML = html;
        document.getElementById('modal-informe').showModal();
    }

    // ==================== AGREGAR ALMACÉN ====================
    function abrirAgregarAlmacen(id, cantidadActual) {
        _articulo_id_almacen = id;
        document.getElementById('almacen-actual').textContent = cantidadActual;
        document.getElementById('input-agregar-cantidad').value = 1;
        document.getElementById('agregar-error').classList.add('hidden');
        document.getElementById('modal-agregar-almacen').showModal();
    }
    async function guardarAgregarAlmacen() {
        const cantidad = parseInt(document.getElementById('input-agregar-cantidad').value);
        const errorEl  = document.getElementById('agregar-error');
        if (!cantidad || cantidad < 1) {
            errorEl.textContent = 'Ingresa una cantidad válida.';
            errorEl.classList.remove('hidden');
            return;
        }
        const res  = await fetch(`${urlBase}/${_articulo_id_almacen}/agregar-almacen`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ cantidad }),
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('modal-agregar-almacen').close();
            location.reload();
        } else {
            errorEl.textContent = 'Error al guardar.';
            errorEl.classList.remove('hidden');
        }
    }

    // ==================== ELIMINAR ====================
    function confirmarEliminar(id) {
        _articulo_id_eliminar = id;
        document.getElementById('modal-eliminar').showModal();
    }
    async function ejecutarEliminar() {
        document.getElementById('modal-eliminar').close();
        if (window._eliminarModo === 'lote') {
            const res = await fetch('{{ route('admin.articulos.destroy-lote') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ ids: window._lote_ids }),
            });
            window._eliminarModo = null;
        } else {
            await fetch(`${urlBase}/${_articulo_id_eliminar}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
        }
        location.reload();
    }

    // ==================== SELECCIÓN LOTE ====================
    function toggleTodos(el) {
        document.querySelectorAll('.check-articulo').forEach(c => c.checked = el.checked);
        actualizarBtnLote();
    }
    function actualizarBtnLote() {
        const seleccionados = document.querySelectorAll('.check-articulo:checked').length;
        const btn = document.getElementById('btn-eliminar-lote');
        btn.classList.toggle('hidden', seleccionados === 0);
        btn.textContent = `Eliminar seleccionados (${seleccionados})`;
    }
    async function eliminarLote() {
        const ids = [...document.querySelectorAll('.check-articulo:checked')].map(c => parseInt(c.value));
        if (!ids.length) return;
        if (!confirm(`¿Eliminar ${ids.length} artículo(s)? Esta acción no se puede deshacer.`)) return;
        const res  = await fetch('{{ route('admin.articulos.destroy-lote') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ ids }),
        });
        const data = await res.json();
        location.reload();
    }

    // ==================== TOGGLE GRUPO ====================
    function toggleGrupo(gid) {
        const filas = document.querySelectorAll(`.grupo-${gid}`);
        const icono = document.getElementById(`icon-${gid}`);
        const estaOculto = filas[0]?.classList.contains('hidden');

        // Mostrar u ocultar filas hijas
        filas.forEach(f => f.classList.toggle('hidden', !estaOculto));
        if (icono) icono.style.transform = estaOculto ? 'rotate(90deg)' : 'rotate(0deg)';

        if (estaOculto) {
            // Cerrar cualquier otro grupo abierto antes de abrir este
            document.querySelectorAll('tbody tr[onclick^="toggleGrupo"]').forEach(tr => {
                const onclickVal = tr.getAttribute('onclick');
                const otroGid = onclickVal.match(/toggleGrupo\('(.+?)'\)/)?.[1];
                if (otroGid && otroGid !== gid) {
                    const otrasFilas = document.querySelectorAll(`.grupo-${otroGid}`);
                    const estaAbierto = !otrasFilas[0]?.classList.contains('hidden');
                    if (estaAbierto) toggleGrupo(otroGid);
                }
            });
            // Scroll hasta la fila cabecera
            const filaCabecera = document.querySelector(`tr[onclick="toggleGrupo('${gid}')"]`);
            if (filaCabecera) {
                setTimeout(() => {
                    filaCabecera.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 50);
            }

            // Atenuar todas las filas que NO pertenecen a este grupo
            document.querySelectorAll('tbody tr').forEach(tr => {
                const esHija   = tr.classList.contains(`grupo-${gid}`);
                const esCabecera = tr.getAttribute('onclick') === `toggleGrupo('${gid}')`;
                if (!esHija && !esCabecera) {
                    tr.classList.add('opacity-30', 'transition-opacity', 'duration-300');
                }
            });

            // Resaltar la cabecera abierta
            filaCabecera?.classList.add('ring-1', 'ring-primary/30');

        } else {
            // Al cerrar: quitar atenuación de todas las filas
            document.querySelectorAll('tbody tr').forEach(tr => {
                tr.classList.remove('opacity-30', 'transition-opacity', 'duration-300');
            });

            const filaCabecera = document.querySelector(`tr[onclick="toggleGrupo('${gid}')"]`);
            filaCabecera?.classList.remove('ring-1', 'ring-primary/30');
        }
    }

    // ==================== ELIMINAR GRUPO ====================
    function confirmarEliminarGrupo(ids, folio) {
        _ids_lote = ids;
        document.getElementById('modal-eliminar').querySelector('p').textContent =
            `Se eliminarán todos los artículos del folio "${folio}" que no tengan relaciones activas. Esta acción no se puede deshacer.`;
        document.getElementById('modal-eliminar').showModal();
        // Sobreescribir temporalmente ejecutarEliminar para este caso
        window._eliminarModo = 'lote';
        window._lote_ids = ids;
    }

    // Extrae la parte numérica final de la serie (sin prefijo)
    function autoSerieNumerico() {
        const serie = document.getElementById('f-serie').value.trim();
        const match = serie.match(/(\d+)$/);
        document.getElementById('f-serie-numerico').value = match ? match[1] : '';
    }

    function toggleGrupoMobile(gid) {
        const contenedor = document.getElementById(`grupo-mobile-${gid}`);
        const icono = document.getElementById(`icon-mobile-${gid}`);
        const estaOculto = contenedor.classList.contains('hidden');

        // Cerrar cualquier otro grupo abierto
        document.querySelectorAll('[id^="grupo-mobile-"]').forEach(el => {
            if (el.id !== `grupo-mobile-${gid}` && !el.classList.contains('hidden')) {
                el.classList.add('hidden');
                const otroGid = el.id.replace('grupo-mobile-', '');
                const otroIcono = document.getElementById(`icon-mobile-${otroGid}`);
                if (otroIcono) otroIcono.style.transform = 'rotate(0deg)';
            }
        });

        contenedor.classList.toggle('hidden', !estaOculto);
        if (icono) icono.style.transform = estaOculto ? 'rotate(90deg)' : 'rotate(0deg)';

        if (estaOculto) {
            setTimeout(() => {
                const cabecera = contenedor.closest('.grupo-mobile-card');
                if (cabecera) {
                    const y = cabecera.getBoundingClientRect().top + window.scrollY - 80;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            }, 50);
        }
    }

    function verDetalleCantidades(folio, almacen, solicitudes, destruccion, perdidos) {
        document.getElementById('detalle-folio').textContent = folio;
        document.getElementById('detalle-almacen').textContent = almacen;
        document.getElementById('detalle-solicitudes').textContent = solicitudes;
        document.getElementById('detalle-destruccion').textContent = destruccion;
        document.getElementById('detalle-perdidos').textContent = perdidos;
        document.getElementById('modal-detalle-cantidades').showModal();
    }
</script>
</x-app-layout>