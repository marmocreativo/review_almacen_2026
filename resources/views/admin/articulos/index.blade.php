<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Inventario</h2>
            <div class="flex gap-2 flex-wrap justify-end">
                <a href="{{ route('admin.articulos.index', array_merge(request()->except(['vista','page']), ['vista' => 'bloques'])) }}"
                    class="btn btn-sm {{ $vista === 'bloques' ? 'btn-primary' : 'btn-ghost' }}">
                    Vista por bloques
                </a>
                <a href="{{ route('admin.articulos.index', array_merge(request()->except(['vista','page']), ['vista' => 'detalle'])) }}"
                    class="btn btn-sm {{ $vista === 'detalle' ? 'btn-primary' : 'btn-ghost' }}">
                    Vista detalle
                </a>
                <a href="{{ route('admin.articulos.exportar') }}" class="btn btn-success btn-sm">↓ Excel</a>
                <button onclick="document.getElementById('modal-individual').showModal()" class="btn btn-primary btn-sm">
                    + Alta individual
                </button>
                <button onclick="document.getElementById('modal-rango').showModal()" class="btn btn-secondary btn-sm">
                    + Alta por rango
                </button>
            </div>
        </div>
    </x-slot>

    <x-alert />

    {{-- FILTROS --}}
    <form method="GET" class="card bg-base-100 shadow mb-4">
        <div class="card-body py-3">
            <input type="hidden" name="vista" value="{{ $vista }}" />
            <div class="flex flex-wrap gap-2 items-end">
                <div class="form-control flex-1 min-w-[200px]">
                    <label class="label py-0"><span class="label-text text-xs">Búsqueda rápida</span></label>
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="ID Item, folio o nombre..."
                        class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control w-32">
                    <label class="label py-0"><span class="label-text text-xs">Tipo</span></label>
                    <select name="tipo" class="select select-bordered select-sm w-full">
                        <option value="">Todos</option>
                        <option value="fisico" {{ request('tipo') === 'fisico' ? 'selected' : '' }}>Físico</option>
                        <option value="digital" {{ request('tipo') === 'digital' ? 'selected' : '' }}>Digital</option>
                    </select>
                </div>
                <div class="form-control w-48">
                    <label class="label py-0"><span class="label-text text-xs">Tipo de examen</span></label>
                    <select name="tipo_examen" class="select select-bordered select-sm w-full">
                        <option value="">Todos</option>
                        @foreach($tiposExamen as $te)
                            <option value="{{ $te->id }}" {{ request('tipo_examen') == $te->id ? 'selected' : '' }}>
                                {{ $te->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-40">
                    <label class="label py-0"><span class="label-text text-xs">Ordenar por</span></label>
                    <select name="orden" class="select select-bordered select-sm w-full">
                        <option value="FOLIO" {{ request('orden', 'FOLIO') === 'FOLIO' ? 'selected' : '' }}>ID Item</option>
                        <option value="NOMBRE" {{ request('orden') === 'NOMBRE' ? 'selected' : '' }}>Nombre</option>
                        <option value="CANTIDAD_ALMACEN" {{ request('orden') === 'CANTIDAD_ALMACEN' ? 'selected' : '' }}>Cantidad almacén</option>
                        <option value="COSTO_UNITARIO" {{ request('orden') === 'COSTO_UNITARIO' ? 'selected' : '' }}>Costo unitario</option>
                    </select>
                </div>
                <div class="form-control w-32">
                    <label class="label py-0"><span class="label-text text-xs">Dirección</span></label>
                    <select name="dir" class="select select-bordered select-sm w-full">
                        <option value="asc" {{ request('dir', 'asc') === 'asc' ? 'selected' : '' }}>Ascendente</option>
                        <option value="desc" {{ request('dir') === 'desc' ? 'selected' : '' }}>Descendente</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm gap-1">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    Filtrar
                </button>
                <a href="{{ route('admin.articulos.index', ['vista' => $vista]) }}" class="btn btn-ghost btn-sm gap-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    Limpiar
                </a>
            </div>
        </div>
    </form>

    {{-- Barra de acciones + tabla + modal de edición (comparten estado Alpine) --}}
    <div x-data="{...seleccionArticulos(), ...edicionArticulo()}" x-cloak>

        <div x-show="seleccionados.length > 0" x-transition
            class="alert alert-warning mb-4 flex items-center justify-between" style="display:none">
            <span><strong x-text="seleccionados.length"></strong> artículo(s) seleccionado(s)</span>
            <button type="button" class="btn btn-error btn-sm" @click="eliminarSeleccionados()">
                <x-heroicon-o-trash class="w-4 h-4" />
                Eliminar seleccionados
            </button>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body p-0 overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="checkbox checkbox-sm" @change="toggleTodos($event)" />
                            </th>
                            <th>ID Item</th>
                            <th>{{ $vista === 'bloques' ? 'Rango' : 'Folio' }}</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Tipo examen</th>
                            <th>Cantidad almacén</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginator as $fila)
                            @if($vista === 'bloques')
                                @php
                                    $ids = $fila['ids'];
                                    $esGrupo = $fila['count'] > 1;
                                    $otras = $fila['cantidad_solicitudes'] + $fila['cantidad_destruccion'] + $fila['cantidad_perdidos'];
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox checkbox-sm row-check"
                                            value="{{ implode(',', $ids) }}"
                                            @change="toggleFila($event)"
                                            {{ !$fila['es_deletable_lote'] ? 'disabled title=Tiene relaciones activas' : '' }} />
                                    </td>
                                    <td class="font-mono">{{ $fila['folio'] }}</td>
                                    <td class="font-mono text-xs">{{ $fila['rango'] }} <span class="text-base-content/40">({{ $fila['count'] }})</span></td>
                                    <td>{{ $fila['nombre'] }}</td>
                                    <td>
                                        <span class="badge badge-sm {{ $fila['tipo'] === 'fisico' ? 'badge-info' : 'badge-accent' }}">
                                            {{ ucfirst($fila['tipo']) }}
                                        </span>
                                    </td>
                                    <td class="text-xs">{{ $fila['tipo_examen'] ?? '—' }}</td>
                                    <td>
                                        <div x-data="{ open: false }">
                                            <div class="flex items-center gap-1">
                                                <span class="font-mono font-semibold">{{ $fila['cantidad_almacen'] }}</span>
                                                @if($otras > 0)
                                                    <button type="button" class="btn btn-ghost btn-xs btn-square" @click="open = !open">
                                                        <span x-text="open ? '−' : '+'"></span>
                                                    </button>
                                                @endif
                                            </div>
                                            @if($otras > 0)
                                                <div x-show="open" x-collapse class="text-xs bg-base-200 rounded p-2 mt-1 space-y-0.5 w-max">
                                                    <p>Solicitudes: <strong>{{ $fila['cantidad_solicitudes'] }}</strong></p>
                                                    <p>Destrucción: <strong>{{ $fila['cantidad_destruccion'] }}</strong></p>
                                                    <p>Perdidos: <strong>{{ $fila['cantidad_perdidos'] }}</strong></p>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td><span class="badge badge-sm {{ $fila['estado']['class'] }}">{{ $fila['estado']['label'] }}</span></td>
                                    <td>
                                        <div class="flex gap-1 justify-end">
                                            @if($esGrupo)
                                                <button type="button" class="btn btn-outline btn-info btn-xs"
                                                    @click="abrirEdicionGrupo({{ implode(',', $ids) }})">Editar grupo</button>
                                            @else
                                                <button type="button" class="btn btn-outline btn-info btn-xs"
                                                    @click="abrirEdicionIndividual({{ $ids[0] }})">Editar</button>
                                            @endif
                                            @if($esGrupo)
                                                <a href="{{ route('admin.articulos.grupo.show', ['ids' => $ids]) }}"
                                                    class="btn btn-ghost btn-xs">Ver grupo</a>
                                            @else
                                                <a href="{{ route('admin.articulos.show', $ids[0]) }}"
                                                    class="btn btn-ghost btn-xs">Ver</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox checkbox-sm row-check"
                                            value="{{ $fila->ID_ARTICULO }}"
                                            @change="toggleFila($event)"
                                            {{ !$fila->esDeletable() ? 'disabled title=Tiene relaciones activas' : '' }} />
                                    </td>
                                    <td class="font-mono">{{ $fila->FOLIO }}</td>
                                    <td class="font-mono text-xs">{{ $fila->SERIE ?: '—' }}</td>
                                    <td>{{ $fila->NOMBRE }}</td>
                                    <td>
                                        <span class="badge badge-sm {{ $fila->TIPO === 'fisico' ? 'badge-info' : 'badge-accent' }}">
                                            {{ ucfirst($fila->TIPO) }}
                                        </span>
                                    </td>
                                    <td class="text-xs">{{ $fila->tipoExamen?->nombre ?? '—' }}</td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <span class="font-mono font-semibold">{{ $fila->CANTIDAD_ALMACEN }}</span>
                                            @php $otras = $fila->CANTIDAD_SOLICITUDES + $fila->CANTIDAD_DESTRUCCION + $fila->CANTIDAD_PERDIDOS; @endphp
                                            @if($otras > 0)
                                                <div class="dropdown dropdown-hover">
                                                    <div tabindex="0" role="button" class="btn btn-ghost btn-xs btn-square">+</div>
                                                    <div tabindex="0" class="dropdown-content z-10 card card-compact w-52 shadow bg-base-100 border border-base-300">
                                                        <div class="card-body text-xs space-y-1">
                                                            <p>Solicitudes: <strong>{{ $fila->CANTIDAD_SOLICITUDES }}</strong></p>
                                                            <p>Destrucción: <strong>{{ $fila->CANTIDAD_DESTRUCCION }}</strong></p>
                                                            <p>Perdidos: <strong>{{ $fila->CANTIDAD_PERDIDOS }}</strong></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php $b = \App\Models\Articulo::estadoBadge($fila->CANTIDAD_ALMACEN, $fila->CANTIDAD_SOLICITUDES, $fila->CANTIDAD_DESTRUCCION, $fila->CANTIDAD_PERDIDOS); @endphp
                                        <span class="badge badge-sm {{ $b['class'] }}">{{ $b['label'] }}</span>
                                    </td>
                                    <td>
                                        <div class="flex gap-1 justify-end">
                                            <button type="button" class="btn btn-outline btn-info btn-xs"
                                                @click="abrirEdicionIndividual({{ $fila->ID_ARTICULO }})">Editar</button>
                                            <a href="{{ route('admin.articulos.show', $fila) }}" class="btn btn-ghost btn-xs">Ver</a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="9" class="text-center text-base-content/50 py-8">Sin resultados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $paginator->links() }}</div>

        {{-- MODAL DE EDICIÓN --}}
        <div x-cloak x-show="open" class="modal" :class="{ 'modal-open': open }">
            <div class="modal-box max-w-xl">
                <h3 class="font-bold text-lg mb-1" x-text="esGrupo ? 'Editar grupo (' + count + ' artículos)' : 'Editar artículo'"></h3>
                <p class="text-sm text-base-content/60 mb-4" x-show="esGrupo">
                    Estos cambios se aplicarán a los <span x-text="count"></span> artículos seleccionados. Folio y número de serie no se modifican aquí.
                </p>

                <div x-show="cargando" class="py-8 text-center text-base-content/50">Cargando...</div>

                <form x-show="!cargando" @submit.prevent="guardar()">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <template x-if="!esGrupo">
                            <div class="form-control sm:col-span-2">
                                <label class="label"><span class="label-text">ID Item *</span></label>
                                <input type="text" x-model="form.FOLIO" class="input input-bordered" required />
                            </div>
                        </template>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Nombre *</span></label>
                            <input type="text" x-model="form.NOMBRE" class="input input-bordered" required />
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Descripción</span></label>
                            <textarea x-model="form.DESCRIPCION" rows="2" class="textarea textarea-bordered"></textarea>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Formato</span></label>
                            <input type="text" x-model="form.FORMATO" class="input input-bordered" />
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Tipo *</span></label>
                            <select x-model="form.TIPO" class="select select-bordered" required>
                                <option value="fisico">Físico</option>
                                <option value="digital">Digital</option>
                            </select>
                        </div>

                        <template x-if="!esGrupo">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Folio</span></label>
                                <input type="text" x-model="form.SERIE" class="input input-bordered" />
                            </div>
                        </template>

                        <template x-if="!esGrupo">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Folio (núm.)</span></label>
                                <input type="text" x-model="form.SERIE_NUMERICO" class="input input-bordered" />
                            </div>
                        </template>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Costo unitario</span></label>
                            <input type="number" step="0.01" min="0" x-model="form.COSTO_UNITARIO" class="input input-bordered" />
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Precio venta</span></label>
                            <input type="number" step="0.01" min="0" x-model="form.PRECIO_VENTA" class="input input-bordered" />
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Tipo de examen relacionado</span></label>
                            <select x-model="form.ID_TIPO_EXAMEN" class="select select-bordered">
                                <option value="">— Sin relación —</option>
                                <template x-for="te in tiposExamen" :key="te.id">
                                    <option :value="te.id" x-text="te.nombre"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <p class="text-error text-sm mt-3" x-show="error" x-text="error"></p>

                    <div class="modal-action">
                        <button type="button" class="btn btn-ghost btn-sm" @click="cerrar()">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm" :disabled="guardando">
                            <span x-show="!guardando">Guardar cambios</span>
                            <span x-show="guardando">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

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
                <div class="form-control col-span-2">
                    <label class="label py-1"><span class="label-text font-medium">Tipo de examen relacionado</span></label>
                    <select id="i-tipo-examen" class="select select-bordered select-sm w-full">
                        <option value="">— Sin relación —</option>
                        @foreach($tiposExamen as $te)
                            <option value="{{ $te->id }}">{{ $te->nombre }}</option>
                        @endforeach
                    </select>
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
                <div class="form-control col-span-2">
                    <label class="label py-1"><span class="label-text font-medium">Tipo de examen relacionado</span></label>
                    <select id="r-tipo-examen" class="select select-bordered select-sm w-full">
                        <option value="">— Sin relación —</option>
                        @foreach($tiposExamen as $te)
                            <option value="{{ $te->id }}">{{ $te->nombre }}</option>
                        @endforeach
                    </select>
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

    <script>
        function edicionArticulo() {
            return {
                open: false,
                cargando: false,
                guardando: false,
                error: '',
                esGrupo: false,
                count: 0,
                ids: [],
                tiposExamen: [],
                form: {},

                async abrirEdicionIndividual(id) {
                    this.esGrupo = false;
                    this.ids = [id];
                    this.open = true;
                    this.cargando = true;
                    this.error = '';
                    try {
                        const res = await fetch(`/admin/articulos/${id}/edit`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        this.tiposExamen = data.tiposExamen;
                        this.form = {
                            FOLIO: data.articulo.FOLIO,
                            NOMBRE: data.articulo.NOMBRE,
                            DESCRIPCION: data.articulo.DESCRIPCION,
                            FORMATO: data.articulo.FORMATO,
                            TIPO: data.articulo.TIPO,
                            SERIE: data.articulo.SERIE,
                            SERIE_NUMERICO: data.articulo.SERIE_NUMERICO,
                            COSTO_UNITARIO: data.articulo.COSTO_UNITARIO,
                            PRECIO_VENTA: data.articulo.PRECIO_VENTA,
                            ID_TIPO_EXAMEN: data.articulo.ID_TIPO_EXAMEN ?? '',
                        };
                    } catch (e) {
                        this.error = 'Error al cargar el artículo.';
                    } finally {
                        this.cargando = false;
                    }
                },

                async abrirEdicionGrupo(idsStr) {
                    const ids = idsStr.toString().split(',').map(Number);
                    this.esGrupo = true;
                    this.ids = ids;
                    this.open = true;
                    this.cargando = true;
                    this.error = '';
                    try {
                        const params = ids.map(id => `ids[]=${id}`).join('&');
                        const res = await fetch(`/admin/articulos/grupo/editar?${params}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        this.tiposExamen = data.tiposExamen;
                        this.count = data.count;
                        this.form = {
                            NOMBRE: data.primero.NOMBRE,
                            DESCRIPCION: data.primero.DESCRIPCION,
                            FORMATO: data.primero.FORMATO,
                            TIPO: data.primero.TIPO,
                            COSTO_UNITARIO: data.primero.COSTO_UNITARIO,
                            PRECIO_VENTA: data.primero.PRECIO_VENTA,
                            ID_TIPO_EXAMEN: data.primero.ID_TIPO_EXAMEN ?? '',
                        };
                    } catch (e) {
                        this.error = 'Error al cargar los artículos.';
                    } finally {
                        this.cargando = false;
                    }
                },

                cerrar() {
                    this.open = false;
                    this.error = '';
                },

                async guardar() {
                    this.guardando = true;
                    this.error = '';
                    try {
                        const url = this.esGrupo
                            ? "{{ route('admin.articulos.grupo.update') }}"
                            : `/admin/articulos/${this.ids[0]}`;

                        const body = this.esGrupo
                            ? { ...this.form, ids: this.ids }
                            : this.form;

                        const res = await fetch(url, {
                            method: this.esGrupo ? 'PATCH' : 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(body),
                        });

                        if (!res.ok) {
                            const data = await res.json().catch(() => ({}));
                            this.error = data.message || 'Ocurrió un error al guardar.';
                            this.guardando = false;
                            return;
                        }

                        window.location.reload();
                    } catch (e) {
                        this.error = 'Error al guardar los cambios.';
                        this.guardando = false;
                    }
                }
            }
        }

        function seleccionArticulos() {
            return {
                seleccionados: [],
                toggleTodos(e) {
                    document.querySelectorAll('.row-check:not(:disabled)').forEach(cb => {
                        cb.checked = e.target.checked;
                        cb.dispatchEvent(new Event('change'));
                    });
                },
                toggleFila(e) {
                    const ids = e.target.value.split(',').map(Number);
                    if (e.target.checked) {
                        this.seleccionados = [...new Set([...this.seleccionados, ...ids])];
                    } else {
                        this.seleccionados = this.seleccionados.filter(id => !ids.includes(id));
                    }
                },
                eliminarSeleccionados() {
                    const ids = this.seleccionados;
                    window.dispatchEvent(new CustomEvent('pin-confirm', {
                        detail: {
                            callback: async () => {
                                const res = await fetch("{{ route('admin.articulos.destroy-lote') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ ids }),
                                });
                                const data = await res.json();
                                if (data.success) {
                                    window.location.reload();
                                }
                            }
                        }
                    }));
                }
            }
        }

        // ==================== ALTA INDIVIDUAL Y RANGO ====================
        const csrfToken = '{{ csrf_token() }}';
        const urlStore  = '{{ route('admin.articulos.store') }}';
        const urlRango  = '{{ route('admin.articulos.rango') }}';

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
                ID_TIPO_EXAMEN: document.getElementById('i-tipo-examen').value || null,
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
                    resumen: `Se agregó correctamente el artículo con ID Item <strong>${data.id_item}</strong>.`,
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
            }
        }

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
                ID_TIPO_EXAMEN: document.getElementById('r-tipo-examen').value || null,
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
                    resumen: `ID Item: <strong>${data.id_item}</strong>`,
                    agregados: data.agregados,
                    existentes: data.existentes,
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
            }
        }

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
    </script>
</x-app-layout>