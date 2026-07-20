<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('admin.ordenes.index') }}" class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold truncate">
                        Orden <span class="font-mono text-primary">{{ $orden->ID_ORDEN }}</span>
                    </h2>
                    <p class="text-xs text-base-content/60 truncate">
                        {{ $orden->FOLIO_FACTURA ? 'Factura: ' . $orden->FOLIO_FACTURA . ' — ' : '' }}
                        {{ \Carbon\Carbon::parse($orden->FECHA_REGISTRO)->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <button onclick="document.getElementById('modal-buscar').showModal()"
                class="btn btn-primary btn-sm gap-1 shrink-0">
                <x-heroicon-o-plus class="w-4 h-4" />
                <span class="hidden sm:inline">Agregar artículo</span>
                <span class="sm:hidden">Agregar</span>
            </button>
        </div>
    </x-slot>

    <x-alert />

    <div class="card bg-base-100 shadow mb-4">
        <div class="card-body p-0">

            {{-- VISTA DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-zebra table-sm">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Serie</th>
                            <th>Nombre</th>
                            <th>Formato</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Costo unit.</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-articulos">
                        @forelse($articulos as $item)
                            <tr id="row-{{ $item->ID }}">
                                <td class="font-mono">{{ $item->FOLIO }}</td>
                                <td class="font-mono">{{ $item->SERIE ?: '—' }}</td>
                                <td>{{ $item->articulo?->NOMBRE ?? '—' }}</td>
                                <td>{{ $item->FORMATO ?: '—' }}</td>
                                <td class="text-center">{{ $item->CANTIDAD }}</td>
                                <td class="text-right">${{ number_format($item->COSTO_UNITARIO, 2) }}</td>
                                <td>
                                    <button onclick="eliminarArticulo({{ $item->ID }})"
                                        class="btn btn-error btn-xs gap-1">
                                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                        Quitar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="row-vacio">
                                <td colspan="7" class="text-center text-base-content/50 py-8">
                                    No hay artículos en esta orden.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL: lista simple --}}
            <div class="md:hidden divide-y divide-base-300" id="lista-mobile">
                @forelse($articulos as $item)
                    <div class="flex items-center gap-3 px-4 py-3" id="row-mobile-{{ $item->ID }}">
                        <div class="flex-1 min-w-0">
                            <p class="font-mono text-sm font-semibold text-primary truncate">{{ $item->FOLIO }}</p>
                            <p class="text-sm truncate">{{ $item->articulo?->NOMBRE ?? '—' }}</p>
                            <p class="text-xs text-base-content/50">Cant: <span class="font-mono font-bold">{{ $item->CANTIDAD }}</span></p>
                        </div>
                        <button onclick="eliminarArticulo({{ $item->ID }})" class="btn btn-ghost btn-sm text-error">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8" id="row-vacio-mobile">
                        No hay artículos en esta orden.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- MODAL BUSCAR --}}
    <dialog id="modal-buscar" class="modal">
        <div class="modal-box w-11/12 max-w-2xl">
            <h3 class="font-bold text-lg mb-4">Agregar artículo a la orden</h3>

            <div class="flex gap-4 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipo_busqueda" value="folio" checked
                        class="radio radio-sm" onchange="tipoBusqueda='folio'" />
                    <span class="text-sm">Por Folio</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipo_busqueda" value="serie"
                        class="radio radio-sm" onchange="tipoBusqueda='serie'" />
                    <span class="text-sm">Por Serie</span>
                </label>
            </div>

            <div class="flex gap-2 mb-4">
                <input type="text" id="input-busqueda" placeholder="Escribe folio o serie..."
                    class="input input-bordered input-sm flex-1" />
                <button onclick="buscarArticulo()" class="btn btn-primary btn-sm gap-1">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    Buscar
                </button>
            </div>

            <div id="resultado-busqueda"></div>

            <div class="modal-action">
                <form method="dialog"><button class="btn btn-ghost btn-sm">Cerrar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- MODAL NUEVO ARTÍCULO --}}
    <dialog id="modal-nuevo" class="modal">
        <div class="modal-box w-11/12 max-w-xl">
            <h3 class="font-bold text-lg mb-1">Nuevo artículo</h3>
            <p class="text-sm text-base-content/50 mb-4" id="nuevo-subtitulo"></p>

            <div class="grid grid-cols-2 gap-3">
                <div class="form-control col-span-2">
                    <label class="label py-1"><span class="label-text font-medium">Folio *</span></label>
                    <input type="text" id="n-folio" class="input input-bordered input-sm font-mono w-full" />
                </div>
                <div class="form-control col-span-2">
                    <label class="label py-1"><span class="label-text font-medium">Nombre *</span></label>
                    <input type="text" id="n-nombre" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Formato</span></label>
                    <input type="text" id="n-formato" class="input input-bordered input-sm w-full"
                        placeholder="Ej: PIEZA..." />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-medium">Tipo</span></label>
                    <select id="n-tipo" class="select select-bordered select-sm w-full">
                        <option value="fisico">Físico</option>
                        <option value="digital">Digital</option>
                    </select>
                </div>
                <div class="form-control col-span-2">
                    <label class="label py-1"><span class="label-text font-medium">Costo unitario</span></label>
                    <label class="input input-bordered input-sm flex items-center gap-1">
                        <span class="text-base-content/40 text-xs">$</span>
                        <input type="number" step="0.01" id="n-costo" value="0" class="grow" />
                    </label>
                </div>
            </div>

            <div id="nuevo-seccion-serie" class="hidden mt-4">
                <div class="divider my-2 text-sm text-base-content/50">Número de serie</div>
                <div class="flex gap-3 items-end">
                    <div class="form-control flex-1">
                        <label class="label py-1"><span class="label-text font-medium">Número de serie *</span></label>
                        <input type="text" id="n-serie"
                            class="input input-bordered input-sm font-mono w-full"
                            oninput="autoNuevoSerieNumerico()" />
                    </div>
                    <div class="form-control w-28">
                        <label class="label py-1">
                            <span class="label-text font-medium text-xs">Parte num.</span>
                        </label>
                        <input type="text" id="n-serie-numerico"
                            class="input input-bordered input-sm font-mono bg-base-200 text-base-content/60 w-full"
                            readonly />
                    </div>
                </div>
            </div>

            <div id="nuevo-seccion-granel" class="hidden mt-4">
                <div class="divider my-2 text-sm text-base-content/50">Cantidad</div>
                <div class="form-control w-full sm:w-48">
                    <label class="label py-1"><span class="label-text font-medium">Cantidad a ingresar *</span></label>
                    <input type="number" id="n-cantidad" value="1" min="1"
                        class="input input-bordered input-sm" />
                </div>
            </div>

            <div id="nuevo-error" class="alert alert-error text-sm mt-3 hidden"></div>

            <div class="modal-action mt-4">
                <button onclick="guardarNuevo()" class="btn btn-primary gap-1">
                    <x-heroicon-o-check class="w-4 h-4" />
                    Guardar
                </button>
                <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

<script>
    const csrfToken    = '{{ csrf_token() }}';
    const urlBuscar    = '{{ route('admin.ordenes.buscar-articulo') }}';
    const urlAgregar   = '{{ route('admin.ordenes.agregar-articulo') }}';
    const urlEliminar  = '{{ url('admin/ordenes/articulo') }}';
    const idOrden      = '{{ $orden->ID_ORDEN }}';
    let tipoBusqueda   = 'folio';
    let _datosBusqueda = null;

    async function buscarArticulo() {
        const busqueda = document.getElementById('input-busqueda').value.trim();
        if (!busqueda) return;
        const tipo = document.querySelector('input[name="tipo_busqueda"]:checked').value;
        const res  = await fetch(urlBuscar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ busqueda, tipo_busqueda: tipo, id_orden: idOrden }),
        });
        const data = await res.json();
        _datosBusqueda = data;
        const cont = document.getElementById('resultado-busqueda');

        switch (data.tipo) {
            case 'serie_encontrada':
                cont.innerHTML = data.en_orden
                    ? `<div class="alert alert-warning"><span>La serie <strong>${data.articulo.SERIE}</strong> ya está en una orden de compra.</span></div>`
                    : `<div class="alert alert-info"><span>Serie <strong>${data.articulo.SERIE}</strong> — ${data.articulo.NOMBRE} / Almacén: ${data.articulo.CANTIDAD_ALMACEN}</span></div>
                       <button onclick="agregarSeries([${data.articulo.ID_ARTICULO}])" class="btn btn-primary btn-sm mt-2 gap-1"><x-heroicon-o-plus class="w-4 h-4"/>Agregar</button>`;
                break;
            case 'serie_nueva':
                cont.innerHTML = `<div class="alert alert-warning"><span>La serie <strong>${data.serie}</strong> no existe.</span></div>
                    <button onclick="abrirNuevo('', '${data.serie}')" class="btn btn-primary btn-sm mt-2">Crear y agregar</button>`;
                break;
            case 'folio_con_series':
                if (!data.disponibles.length) {
                    cont.innerHTML = `<div class="alert alert-warning"><span>Todas las series del folio <strong>${data.folio}</strong> ya están en órdenes.</span></div>`;
                } else {
                    const filas = data.disponibles.map(a => `
                        <tr>
                            <td><input type="checkbox" class="checkbox checkbox-sm check-serie" value="${a.ID_ARTICULO}" checked /></td>
                            <td class="font-mono text-sm">${a.SERIE}</td>
                            <td class="font-mono text-sm">${a.SERIE_NUMERICO}</td>
                            <td class="text-center">${a.CANTIDAD_ALMACEN}</td>
                        </tr>`).join('');
                    cont.innerHTML = `<div class="alert alert-info mb-3"><span>${data.disponibles.length} series disponibles de ${data.total} totales.</span></div>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto mb-3">
                            <table class="table table-sm">
                                <thead><tr>
                                    <th><input type="checkbox" class="checkbox checkbox-sm" onchange="toggleTodos(this)" checked /></th>
                                    <th>Serie</th><th>Núm.</th><th>Almacén</th>
                                </tr></thead>
                                <tbody>${filas}</tbody>
                            </table>
                        </div>
                        <button onclick="agregarSeleccionadas()" class="btn btn-primary btn-sm gap-1">
                            <x-heroicon-o-plus class="w-4 h-4"/>Agregar seleccionadas
                        </button>`;
                }
                break;
            case 'folio_granel':
                cont.innerHTML = `<div class="alert alert-info mb-3"><span>Folio <strong>${data.folio}</strong> — ${data.articulo.NOMBRE} / Almacén: <strong>${data.articulo.CANTIDAD_ALMACEN}</strong></span></div>
                    <div class="flex items-end gap-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-xs">Cantidad</span></label>
                            <input type="number" id="cantidad-granel" value="1" min="1" class="input input-bordered input-sm w-28" />
                        </div>
                        <button onclick="agregarGranel(${data.articulo.ID_ARTICULO})" class="btn btn-primary btn-sm">Agregar</button>
                    </div>`;
                break;
            case 'folio_nuevo':
                cont.innerHTML = `<div class="alert alert-warning"><span>El folio <strong>${data.folio}</strong> no existe.</span></div>
                    <button onclick="abrirNuevo('${data.folio}', '')" class="btn btn-primary btn-sm mt-2">Crear y agregar</button>`;
                break;
        }
    }

    function toggleTodos(checkbox) {
        document.querySelectorAll('.check-serie').forEach(c => c.checked = checkbox.checked);
    }

    async function agregarSeleccionadas() {
        const ids = [...document.querySelectorAll('.check-serie:checked')].map(c => parseInt(c.value));
        if (!ids.length) { alert('Selecciona al menos una serie.'); return; }
        await agregarSeries(ids);
    }

    async function agregarSeries(ids) {
        const res  = await fetch(urlAgregar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ id_orden: idOrden, tipo: 'series', ids }),
        });
        const data = await res.json();
        if (data.success) { document.getElementById('modal-buscar').close(); location.reload(); }
    }

    async function agregarGranel(idArticulo) {
        const cantidad = parseInt(document.getElementById('cantidad-granel').value);
        const res  = await fetch(urlAgregar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ id_orden: idOrden, tipo: 'granel', id_articulo: idArticulo, cantidad }),
        });
        const data = await res.json();
        if (data.success) { document.getElementById('modal-buscar').close(); location.reload(); }
    }

    function abrirNuevo(folio, serie) {
        document.getElementById('n-folio').value          = folio;
        document.getElementById('n-nombre').value         = '';
        document.getElementById('n-formato').value        = '';
        document.getElementById('n-costo').value          = '0';
        document.getElementById('n-serie').value          = serie;
        document.getElementById('n-serie-numerico').value = '';
        document.getElementById('nuevo-error').classList.add('hidden');
        document.getElementById('nuevo-subtitulo').textContent = folio ? `Folio: ${folio}` : '';
        const tieneSerie = serie !== '';
        document.getElementById('nuevo-seccion-serie').classList.toggle('hidden', !tieneSerie);
        document.getElementById('nuevo-seccion-granel').classList.toggle('hidden', tieneSerie);
        if (tieneSerie) autoNuevoSerieNumerico();
        document.getElementById('modal-buscar').close();
        document.getElementById('modal-nuevo').showModal();
    }

    function autoNuevoSerieNumerico() {
        const serie = document.getElementById('n-serie').value.trim();
        const match = serie.match(/(\d+)$/);
        document.getElementById('n-serie-numerico').value = match ? match[1] : '';
    }

    async function guardarNuevo() {
        const serie      = document.getElementById('n-serie').value.trim();
        const tieneSerie = serie !== '';
        const payload = {
            id_orden:       idOrden,
            tipo:           'nuevo',
            folio:          document.getElementById('n-folio').value.trim(),
            nombre:         document.getElementById('n-nombre').value.trim(),
            SERIE:          serie,
            SERIE_NUMERICO: document.getElementById('n-serie-numerico').value.trim(),
            formato:        document.getElementById('n-formato').value.trim(),
            tipo_articulo:  document.getElementById('n-tipo').value,
            costo_unitario: document.getElementById('n-costo').value,
            cantidad:       tieneSerie ? 1 : parseInt(document.getElementById('n-cantidad').value),
        };
        const res  = await fetch(urlAgregar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload),
        });
        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            const mensajes = data.errors
                ? Object.values(data.errors).flat().join(' | ')
                : (data.message ?? `Error ${res.status}`);
            document.getElementById('nuevo-error').textContent = mensajes;
            document.getElementById('nuevo-error').classList.remove('hidden');
            return;
        }
        const data = await res.json();
        if (data.success) {
            document.getElementById('modal-nuevo').close();
            location.reload();
        } else {
            document.getElementById('nuevo-error').textContent = data.message ?? 'Error al guardar.';
            document.getElementById('nuevo-error').classList.remove('hidden');
        }
    }

    async function eliminarArticulo(id) {
        if (!confirm('¿Quitar este artículo de la orden? Se revertirá la cantidad en almacén.')) return;
        const res  = await fetch(`${urlEliminar}/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById(`row-${id}`)?.remove();
            document.getElementById(`row-mobile-${id}`)?.remove();
        }
    }
</script>
</x-app-layout>