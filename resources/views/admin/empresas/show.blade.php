<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('admin.empresas.index') }}" class="btn btn-ghost btn-sm btn-square shrink-0">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <div class="avatar shrink-0">
                    <div class="mask mask-squircle w-8 h-8">
                        <img src="{{ asset('storage/' . $empresa->logo) }}"
                            onerror="this.src='https://placehold.co/32x32?text=E'"
                            alt="{{ $empresa->nombre }}" />
                    </div>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold truncate">{{ $empresa->nombre }}</h2>
                    <p class="text-xs text-base-content/50 truncate hidden sm:block">{{ $empresa->razon_social ?? '' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="badge {{ $empresa->estado === 'activo' ? 'badge-success' : 'badge-error' }}">
                    {{ $empresa->estado }}
                </span>
                <a href="{{ route('admin.empresas.edit', $empresa) }}" class="btn btn-info btn-sm gap-1">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    <span class="hidden sm:inline">Editar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <x-alert />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

        {{-- ═══════════ COLUMNA 1/4: DATOS GENERALES ═══════════ --}}
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow h-fit">
                <div class="card-body">
                    <h3 class="card-title text-base mb-2">Datos generales</h3>

                    <div class="text-sm space-y-3">
                        <div>
                            <span class="text-xs text-base-content/50 block">Tipo de cliente</span>
                            <span class="font-medium">{{ $empresa->tipo_cliente ? ucfirst($empresa->tipo_cliente) : '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">RFC</span>
                            <span class="font-mono font-medium">{{ $empresa->rfc ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Razón social</span>
                            <span class="font-medium">{{ $empresa->razon_social ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Dirección fiscal</span>
                            <span class="font-medium">{{ $empresa->direccion_fiscal ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Uso de CFDI</span>
                            <span class="font-medium">{{ $empresa->uso_de_cfdi ?: '—' }}</span>
                        </div>

                        <div class="divider my-0"></div>

                        <div>
                            <span class="text-xs text-base-content/50 block">Sedes</span>
                            <span class="font-medium">{{ $empresa->sedes->count() }}</span>
                        </div>

                        <div class="divider my-0"></div>

                        <h4 class="text-xs font-semibold uppercase text-base-content/50">Contrato</h4>
                        <div>
                            <span class="text-xs text-base-content/50 block">Fecha de contrato</span>
                            <span class="font-medium">{{ $empresa->fecha_de_contrato?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Vigencia (meses)</span>
                            <span class="font-medium">{{ $empresa->vigencia_contrato ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Días de crédito</span>
                            <span class="font-medium">{{ $empresa->dias_de_credito ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Portal de facturación</span>
                            @if($empresa->portal_de_facturacion)
                                <a href="{{ $empresa->portal_de_facturacion }}" target="_blank" class="link link-primary font-medium break-all">{{ $empresa->portal_de_facturacion }}</a>
                            @else
                                <span class="font-medium">—</span>
                            @endif
                        </div>

                        <div class="divider my-0"></div>

                        <h4 class="text-xs font-semibold uppercase text-base-content/50">Contacto operativo</h4>
                        <div>
                            <span class="text-xs text-base-content/50 block">Nombre</span>
                            <span class="font-medium">{{ trim(($empresa->nombre_contacto_operativo ?? '') . ' ' . ($empresa->apellido_contacto_operativo ?? '')) ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Teléfono</span>
                            <span class="font-medium">{{ $empresa->telefono_contacto_operativo ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Correo</span>
                            @if($empresa->email_contacto_operativo)
                                <a href="mailto:{{ $empresa->email_contacto_operativo }}" class="link link-primary font-medium break-all">{{ $empresa->email_contacto_operativo }}</a>
                            @else
                                <span class="font-medium">—</span>
                            @endif
                        </div>

                        <div class="divider my-0"></div>

                        <h4 class="text-xs font-semibold uppercase text-base-content/50">Contacto de facturación</h4>
                        <div>
                            <span class="text-xs text-base-content/50 block">Nombre</span>
                            <span class="font-medium">{{ trim(($empresa->nombre_contacto_facturacion ?? '') . ' ' . ($empresa->apellido_contacto_facturacion ?? '')) ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Teléfono</span>
                            <span class="font-medium">{{ $empresa->telefono_contacto_facturacion ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Correo</span>
                            @if($empresa->email_contacto_facturacion)
                                <a href="mailto:{{ $empresa->email_contacto_facturacion }}" class="link link-primary font-medium break-all">{{ $empresa->email_contacto_facturacion }}</a>
                            @else
                                <span class="font-medium">—</span>
                            @endif
                        </div>

                        @if($empresa->notas)
                            <div class="divider my-0"></div>
                            <div>
                                <span class="text-xs text-base-content/50 block">Notas</span>
                                <span class="font-medium whitespace-pre-line">{{ $empresa->notas }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ COLUMNA 3/4: TABS (SEDES / CONTACTOS / SOLICITUDES) ═══════════ --}}
        <div class="lg:col-span-3" x-data="{ tab: '{{ $tabActiva }}' }">

            <div role="tablist" class="tabs tabs-boxed mb-4">
                <button type="button" role="tab" class="tab" :class="{ 'tab-active': tab === 'sedes' }" @click="tab = 'sedes'">Sedes</button>
                <button type="button" role="tab" class="tab" :class="{ 'tab-active': tab === 'contactos' }" @click="tab = 'contactos'">Contactos</button>
                <button type="button" role="tab" class="tab" :class="{ 'tab-active': tab === 'solicitudes' }" @click="tab = 'solicitudes'">Historial de solicitudes</button>
            </div>

            {{-- ───── TAB: SEDES ───── --}}
            <div x-show="tab === 'sedes'" x-cloak
                x-data="sedesPanel({{ $empresa->id }})" x-init="cargar()">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="card-title text-base">Sedes</h3>
                            <button type="button" class="btn btn-ghost btn-xs gap-1" @click="abrirNueva()">
                                <x-heroicon-o-plus class="w-3.5 h-3.5" />
                                Nueva
                            </button>
                        </div>

                        <div x-show="cargando" class="text-sm text-base-content/40 text-center py-4">Cargando...</div>

                        <template x-if="!cargando && sedes.length === 0">
                            <p class="text-sm text-base-content/50 text-center py-4">No hay sedes registradas.</p>
                        </template>

                        <template x-for="sede in sedes" :key="sede.id">
                            <div class="collapse collapse-arrow bg-base-200 mb-2 rounded-lg">
                                <input type="checkbox" />
                                <div class="collapse-title text-sm font-medium py-3 min-h-0">
                                    <div class="flex items-center justify-between pr-4">
                                        <span x-text="sede.nombre"></span>
                                        <span class="badge badge-sm" :class="sede.estado === 'activo' ? 'badge-success' : 'badge-ghost'" x-text="sede.estado"></span>
                                    </div>
                                </div>
                                <div class="collapse-content text-sm">
                                    <p class="text-base-content/70 mb-1" x-show="direccionSede(sede)" x-text="direccionSede(sede)"></p>
                                    <p class="text-base-content/50 text-xs mb-2" x-show="sede.referencias" x-text="sede.referencias"></p>
                                    <p class="text-xs text-base-content/40 mt-2" x-text="'Contactos: ' + (sede.contactos_count ?? 0)"></p>

                                    <div class="flex gap-2 mt-3">
                                        <button type="button" class="btn btn-ghost btn-xs" @click="abrirEditar(sede)">Editar sede</button>
                                        <button type="button" class="btn btn-ghost btn-xs text-error" @click="eliminar(sede)">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- MODAL: NUEVA / EDITAR SEDE --}}
                        <div x-show="modalAbierto" x-cloak class="modal" :class="{ 'modal-open': modalAbierto }">
                            <div class="modal-box max-w-lg">
                                <h3 class="font-bold text-lg mb-4" x-text="editando ? 'Editar sede' : 'Nueva sede'"></h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="form-control sm:col-span-2">
                                        <label class="label py-1"><span class="label-text text-xs">Nombre *</span></label>
                                        <input type="text" x-model="form.nombre" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Calle y número</span></label>
                                        <input type="text" x-model="form.calle_y_numero" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Colonia/Barrio</span></label>
                                        <input type="text" x-model="form.colonia_barrio" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Alcaldía/Municipio</span></label>
                                        <input type="text" x-model="form.alcaldia_municipio" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Ciudad</span></label>
                                        <input type="text" x-model="form.ciudad" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Estado</span></label>
                                        <input type="text" x-model="form.estado_republica" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Código postal</span></label>
                                        <input type="text" x-model="form.codigo_postal" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control sm:col-span-2">
                                        <label class="label py-1"><span class="label-text text-xs">Referencias</span></label>
                                        <textarea x-model="form.referencias" rows="2" class="textarea textarea-bordered textarea-sm w-full"></textarea>
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Estatus</span></label>
                                        <select x-model="form.estado" class="select select-bordered select-sm w-full">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                    </div>
                                </div>

                                <p class="text-error text-sm mt-3" x-show="error" x-text="error"></p>

                                <div class="modal-action">
                                    <button type="button" class="btn btn-ghost btn-sm" @click="cerrarModal()">Cancelar</button>
                                    <button type="button" class="btn btn-primary btn-sm" @click="guardar()" :disabled="guardando">
                                        <span x-show="!guardando">Guardar</span>
                                        <span x-show="guardando">Guardando...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ───── TAB: CONTACTOS ───── --}}
            <div x-show="tab === 'contactos'" x-cloak
                x-data="contactosPanel({{ $empresa->id }})" x-init="cargar()">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="card-title text-base">Contactos</h3>
                            <div class="flex gap-2">
                                <button type="button" class="btn btn-ghost btn-xs gap-1" @click="abrirNuevo()">
                                    <x-heroicon-o-plus class="w-3.5 h-3.5" />
                                    Nuevo
                                </button>
                                <a href="{{ route('admin.empresas.contactos.index', $empresa) }}"
                                    class="btn btn-ghost btn-xs gap-1">
                                    <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                                    Ver todos
                                </a>
                            </div>
                        </div>

                        <div x-show="cargando" class="text-sm text-base-content/40 text-center py-4">Cargando...</div>

                        <template x-if="!cargando && contactos.length === 0">
                            <p class="text-sm text-base-content/50 text-center py-4">No hay contactos registrados.</p>
                        </template>

                        <template x-for="contacto in contactosVisibles" :key="contacto.id">
                            <div class="flex items-center justify-between py-2 border-b border-base-300 last:border-0 text-sm gap-2">
                                <div class="min-w-0">
                                    <p class="font-medium truncate" x-text="contacto.nombre + ' ' + contacto.apellidos"></p>
                                    <p class="text-xs text-base-content/50 truncate">
                                        <span x-text="contacto.correo || '—'"></span>
                                        <span x-show="contacto.telefono" x-text="' · ' + contacto.telefono"></span>
                                    </p>
                                    <div class="flex flex-wrap gap-1 mt-1" x-show="contacto.sedes && contacto.sedes.length">
                                        <template x-for="s in contacto.sedes" :key="s.id">
                                            <span class="badge badge-ghost badge-xs" x-text="s.nombre"></span>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-1 mt-1">
                                        <template x-if="contacto.pin">
                                            <span class="font-mono text-xs text-base-content/60" x-text="'PIN: ' + contacto.pin"></span>
                                        </template>
                                        <template x-if="!contacto.pin">
                                            <span class="text-xs text-base-content/40">Sin PIN</span>
                                        </template>
                                        <button type="button" class="btn btn-ghost btn-xs btn-square" x-show="contacto.pin"
                                            @click="copiarPin(contacto.pin)" title="Copiar PIN">
                                            <x-heroicon-o-clipboard class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </div>
                                <div class="flex gap-1 shrink-0">
                                    <button type="button" class="btn btn-ghost btn-xs btn-square" title="Enviar PIN por correo"
                                        @click="enviarPin(contacto)" :disabled="enviandoPinId === contacto.id">
                                        <x-heroicon-o-envelope class="w-3.5 h-3.5" />
                                    </button>
                                    <button type="button" class="btn btn-ghost btn-xs btn-square" @click="abrirEditar(contacto)">
                                        <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- MODAL: NUEVO / EDITAR CONTACTO --}}
                        <div x-show="modalAbierto" x-cloak class="modal" :class="{ 'modal-open': modalAbierto }">
                            <div class="modal-box max-w-lg">
                                <h3 class="font-bold text-lg mb-4" x-text="editando ? 'Editar contacto' : 'Nuevo contacto'"></h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Nombre *</span></label>
                                        <input type="text" x-model="form.nombre" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Apellidos *</span></label>
                                        <input type="text" x-model="form.apellidos" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Teléfono</span></label>
                                        <input type="text" x-model="form.telefono" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text text-xs">Correo</span></label>
                                        <input type="email" x-model="form.correo" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div class="form-control sm:col-span-2">
                                        <label class="label py-1"><span class="label-text text-xs">Sedes asignadas</span></label>
                                        <div class="flex flex-wrap gap-3 p-2 border border-base-300 rounded-lg max-h-32 overflow-y-auto">
                                            <template x-for="sede in sedesActivas" :key="sede.id">
                                                <label class="label cursor-pointer gap-2 py-0">
                                                    <input type="checkbox" class="checkbox checkbox-sm" :value="sede.id"
                                                        @change="toggleSede($event, sede.id)"
                                                        :checked="form.sedes.includes(sede.id)" />
                                                    <span class="label-text text-xs" x-text="sede.nombre"></span>
                                                </label>
                                            </template>
                                            <span class="text-xs text-base-content/40" x-show="sedesActivas.length === 0">No hay sedes activas.</span>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-error text-sm mt-3" x-show="error" x-text="error"></p>

                                <div class="modal-action">
                                    <button type="button" class="btn btn-ghost btn-sm" @click="cerrarModal()">Cancelar</button>
                                    <button type="button" class="btn btn-primary btn-sm" @click="guardar()" :disabled="guardando">
                                        <span x-show="!guardando">Guardar</span>
                                        <span x-show="guardando">Guardando...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ───── TAB: HISTORIAL DE SOLICITUDES ───── --}}
            <div x-show="tab === 'solicitudes'" x-cloak>
                <div class="card md:bg-base-100 md:shadow">
                    <div class="card-body p-0">
                        <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                            <h3 class="font-semibold text-base">Historial de solicitudes</h3>
                            <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-ghost btn-xs gap-1">
                                <x-heroicon-o-plus class="w-3.5 h-3.5" />
                                Nueva
                            </a>
                        </div>

                        <div class="divide-y divide-base-300">
                            @forelse($solicitudes as $solicitud)
                                <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                    class="flex items-center justify-between px-4 py-3 hover:bg-base-200 transition-colors">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-sm">#{{ $solicitud->ID_SOLICITUD }}</span>
                                            <span class="text-base-content/50 text-sm truncate">{{ $solicitud->sede?->nombre }}</span>
                                        </div>
                                        <p class="text-xs text-base-content/40 mt-0.5">
                                            {{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}
                                            · {{ $solicitud->examenes_count }} {{ $solicitud->examenes_count === 1 ? 'examen' : 'exámenes' }}
                                        </p>
                                    </div>
                                    @php
                                        $badge = match($solicitud->ESTADO_SOLICITUD) {
                                            'pendiente' => 'badge-warning',
                                            'enviada'   => 'badge-info',
                                            'retornada' => 'badge-success',
                                            default     => 'badge-ghost',
                                        };
                                    @endphp
                                    <span class="badge badge-sm {{ $badge }} shrink-0 ml-2">
                                        {{ $solicitud->ESTADO_SOLICITUD }}
                                    </span>
                                </a>
                            @empty
                                <div class="text-center text-base-content/50 py-8 text-sm">
                                    No hay solicitudes para esta empresa.
                                </div>
                            @endforelse
                        </div>

                        @if($solicitudes->hasPages())
                            <div class="px-4 py-3">{{ $solicitudes->appends(['tab' => 'solicitudes'])->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

<script>
    const csrfToken = '{{ csrf_token() }}';

    function sedesPanel(empresaId) {
        return {
            empresaId,
            sedes: [],
            cargando: true,
            modalAbierto: false,
            editando: false,
            guardando: false,
            error: '',
            sedeActualId: null,
            form: {},

            defaultForm() {
                return {
                    nombre: '', calle_y_numero: '', colonia_barrio: '', alcaldia_municipio: '',
                    ciudad: '', estado_republica: '', codigo_postal: '', referencias: '', estado: 'activo',
                };
            },

            async cargar() {
                this.cargando = true;
                try {
                    const res = await fetch(`/admin/empresas/${this.empresaId}/sedes-json`);
                    this.sedes = await res.json();
                } finally {
                    this.cargando = false;
                }
            },

            direccionSede(sede) {
                return [sede.calle_y_numero, sede.colonia_barrio, sede.alcaldia_municipio, sede.ciudad, sede.estado_republica, sede.codigo_postal]
                    .filter(Boolean).join(', ');
            },

            abrirNueva() {
                this.editando = false;
                this.sedeActualId = null;
                this.form = this.defaultForm();
                this.error = '';
                this.modalAbierto = true;
            },

            abrirEditar(sede) {
                this.editando = true;
                this.sedeActualId = sede.id;
                this.form = {
                    nombre: sede.nombre ?? '',
                    calle_y_numero: sede.calle_y_numero ?? '',
                    colonia_barrio: sede.colonia_barrio ?? '',
                    alcaldia_municipio: sede.alcaldia_municipio ?? '',
                    ciudad: sede.ciudad ?? '',
                    estado_republica: sede.estado_republica ?? '',
                    codigo_postal: sede.codigo_postal ?? '',
                    referencias: sede.referencias ?? '',
                    estado: sede.estado ?? 'activo',
                };
                this.error = '';
                this.modalAbierto = true;
            },

            cerrarModal() {
                this.modalAbierto = false;
            },

            async guardar() {
                this.error = '';
                if (!this.form.nombre) {
                    this.error = 'El nombre es requerido.';
                    return;
                }
                this.guardando = true;
                try {
                    const url = this.editando
                        ? `/admin/empresas/${this.empresaId}/sedes/${this.sedeActualId}`
                        : `/admin/empresas/${this.empresaId}/sedes`;

                    const res = await fetch(url, {
                        method: this.editando ? 'PUT' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });
                    const data = await res.json().catch(() => ({}));

                    if (!res.ok || !data.success) {
                        this.error = data.message || 'Error al guardar la sede.';
                        return;
                    }

                    this.modalAbierto = false;
                    await this.cargar();
                } catch (e) {
                    this.error = 'Error al guardar la sede.';
                } finally {
                    this.guardando = false;
                }
            },

            async eliminar(sede) {
                if (!confirm(`¿Eliminar la sede "${sede.nombre}"?`)) return;
                try {
                    const res = await fetch(`/admin/empresas/${this.empresaId}/sedes/${sede.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json().catch(() => ({}));
                    if (data.success) {
                        await this.cargar();
                    } else {
                        alert(data.message || 'No se pudo eliminar la sede.');
                    }
                } catch (e) {
                    alert('Error al eliminar la sede.');
                }
            },
        }
    }

    function contactosPanel(empresaId) {
        return {
            empresaId,
            contactos: [],
            sedesActivas: [],
            cargando: true,
            modalAbierto: false,
            editando: false,
            guardando: false,
            error: '',
            contactoActualId: null,
            enviandoPinId: null,
            form: {},

            get contactosVisibles() {
                return this.contactos.slice(0, 5);
            },

            copiarPin(pin) {
                navigator.clipboard.writeText(pin);
            },

            async enviarPin(contacto) {
                this.enviandoPinId = contacto.id;
                try {
                    const res = await fetch(`/admin/empresas/${this.empresaId}/contactos/${contacto.id}/enviar-pin`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json().catch(() => ({}));
                    if (data.success) {
                        await this.cargar();
                        alert(data.message || 'PIN enviado correctamente.');
                    } else {
                        alert(data.message || 'No se pudo enviar el PIN.');
                    }
                } catch (e) {
                    alert('Error al enviar el PIN.');
                } finally {
                    this.enviandoPinId = null;
                }
            },

            defaultForm() {
                return { nombre: '', apellidos: '', telefono: '', correo: '', sedes: [] };
            },

            async cargar() {
                this.cargando = true;
                try {
                    const [resContactos, resSedes] = await Promise.all([
                        fetch(`/admin/empresas/${this.empresaId}/contactos-json`),
                        fetch(`/admin/empresas/${this.empresaId}/sedes-json`),
                    ]);
                    this.contactos = await resContactos.json();
                    const sedes = await resSedes.json();
                    this.sedesActivas = sedes.filter(s => s.estado === 'activo');
                } finally {
                    this.cargando = false;
                }
            },

            toggleSede(e, sedeId) {
                if (e.target.checked) {
                    this.form.sedes = [...new Set([...this.form.sedes, sedeId])];
                } else {
                    this.form.sedes = this.form.sedes.filter(id => id !== sedeId);
                }
            },

            abrirNuevo() {
                this.editando = false;
                this.contactoActualId = null;
                this.form = this.defaultForm();
                this.error = '';
                this.modalAbierto = true;
            },

            abrirEditar(contacto) {
                this.editando = true;
                this.contactoActualId = contacto.id;
                this.form = {
                    nombre: contacto.nombre ?? '',
                    apellidos: contacto.apellidos ?? '',
                    telefono: contacto.telefono ?? '',
                    correo: contacto.correo ?? '',
                    sedes: (contacto.sedes || []).map(s => s.id),
                };
                this.error = '';
                this.modalAbierto = true;
            },

            cerrarModal() {
                this.modalAbierto = false;
            },

            async guardar() {
                this.error = '';
                if (!this.form.nombre || !this.form.apellidos) {
                    this.error = 'Nombre y apellidos son requeridos.';
                    return;
                }
                this.guardando = true;
                try {
                    const url = this.editando
                        ? `/admin/empresas/${this.empresaId}/contactos/${this.contactoActualId}`
                        : `/admin/empresas/${this.empresaId}/contactos`;

                    const res = await fetch(url, {
                        method: this.editando ? 'PUT' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });
                    const data = await res.json().catch(() => ({}));

                    if (!res.ok || !data.success) {
                        this.error = data.message || 'Error al guardar el contacto.';
                        return;
                    }

                    this.modalAbierto = false;
                    await this.cargar();
                } catch (e) {
                    this.error = 'Error al guardar el contacto.';
                } finally {
                    this.guardando = false;
                }
            },
        }
    }
</script>
</x-app-layout>