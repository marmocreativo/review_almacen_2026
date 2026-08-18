<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-ghost btn-sm">←</a>
            <h2 class="text-xl font-semibold">Nueva solicitud</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-3xl" x-data="solicitudForm()">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.solicitudes.store') }}">
                @csrf

                {{-- Empresa / Sede / Contacto --}}
                <h3 class="font-semibold text-base mb-3">Destino</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Empresa *</span></label>
                        <select name="ID_EMPRESA" class="select select-bordered @error('ID_EMPRESA') select-error @enderror"
                            @change="onEmpresaChange($event.target.value)">
                            <option value="">Seleccionar...</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}" {{ old('ID_EMPRESA') == $empresa->id ? 'selected' : '' }}>
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('ID_EMPRESA')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                        <button type="button" class="btn btn-ghost btn-xs mt-1 justify-start"
                            onclick="document.getElementById('modal-nueva-empresa').showModal()">
                            + Nueva empresa
                        </button>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Sede *</span></label>
                        <select name="ID_SEDE" class="select select-bordered @error('ID_SEDE') select-error @enderror"
                            @change="onSedeChange($event.target.value)"
                            :disabled="!sedes.length">
                            <option value="">Seleccionar...</option>
                            <template x-for="sede in sedes" :key="sede.id">
                                <option :value="sede.id" x-text="sede.nombre"></option>
                            </template>
                        </select>
                        @error('ID_SEDE')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                        <button type="button" class="btn btn-ghost btn-xs mt-1 justify-start"
                            x-show="empresaId"
                            onclick="document.getElementById('modal-nueva-sede').showModal()">
                            + Nueva sede
                        </button>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Contacto *</span></label>
                        <select name="ID_CONTACTO" class="select select-bordered @error('ID_CONTACTO') select-error @enderror"
                            :disabled="!contactos.length"
                            @change="onContactoChange($event.target.value)">
                            <option value="">Seleccionar...</option>
                            <template x-for="contacto in contactos" :key="contacto.id">
                                <option :value="contacto.id" x-text="contacto.nombre + ' ' + contacto.apellidos"></option>
                            </template>
                        </select>
                        @error('ID_CONTACTO')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                        <button type="button" class="btn btn-ghost btn-xs mt-1 justify-start"
                            x-show="sedeId"
                            onclick="document.getElementById('modal-nuevo-contacto').showModal()">
                            + Nuevo contacto
                        </button>
                    </div>
                </div>

                <div class="divider"></div>

                {{-- Responsable --}}
                <h3 class="font-semibold text-base mb-3">Responsable</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Título</span></label>
                        <input type="text" name="RESPONSABLE_TITULO"
                            x-model="responsable.titulo"
                            placeholder="Ej: Coordinador académico"
                            class="input input-bordered @error('RESPONSABLE_TITULO') input-error @enderror" />
                        @error('RESPONSABLE_TITULO')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nombre *</span></label>
                        <input type="text" name="RESPONSABLE_NOMBRE"
                            x-model="responsable.nombre"
                            class="input input-bordered @error('RESPONSABLE_NOMBRE') input-error @enderror" />
                        @error('RESPONSABLE_NOMBRE')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Correo</span></label>
                        <input type="email" name="RESPONSABLE_CORREO"
                            x-model="responsable.correo"
                            class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Teléfono</span></label>
                        <input type="text" name="RESPONSABLE_TELEFONO"
                            x-model="responsable.telefono"
                            class="input input-bordered" />
                    </div>
                </div>

                <div class="divider"></div>

                {{-- Exámenes (repeater) --}}
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-base">Exámenes</h3>
                    <button type="button" class="btn btn-primary btn-xs" @click="agregarExamenFila()">
                        + Agregar examen
                    </button>
                </div>

                <div class="space-y-2 mb-2">
                    <template x-for="(fila, index) in examenes" :key="fila.uid">
                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_140px_auto] gap-2 items-end">
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Tipo de Examen *</span></label>
                                <select :name="`examenes[${index}][tipo_examen_id]`"
                                    x-model="fila.tipo_examen_id"
                                    class="select select-bordered select-sm w-full">
                                    <option value="">Seleccionar...</option>
                                    @foreach($tiposExamen as $te)
                                        <option value="{{ $te->id }}">{{ $te->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Cantidad exámenes *</span></label>
                                <input type="number" min="1" :name="`examenes[${index}][cantidad]`"
                                    x-model="fila.cantidad"
                                    class="input input-bordered input-sm w-full" />
                            </div>
                            <button type="button" class="btn btn-ghost btn-sm btn-square text-error"
                                x-show="examenes.length > 1"
                                @click="quitarExamenFila(index)">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    </template>
                </div>
                @error('examenes')<p class="text-error text-sm mb-4">{{ $message }}</p>@enderror

                <div class="divider"></div>

                {{-- Logística --}}
                <h3 class="font-semibold text-base mb-3">Logística</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Fecha primer aplicación *</span></label>
                        <input type="date" name="FECHA_PRIMERA_APLICACION" value="{{ old('FECHA_PRIMERA_APLICACION') }}"
                            x-model="fechaPrimeraAplicacion"
                            class="input input-bordered @error('FECHA_PRIMERA_APLICACION') input-error @enderror" />
                        <p class="text-xs text-base-content/40 mt-1" x-show="sedeId" x-text="'Fecha recomendada: ' + fechaMinima + (envioZona === 'cdmx_area_metropolitana' ? ' (10 días, zona metropolitana)' : ' (15 días, foráneo)')"></p>
                        <p class="text-xs text-warning mt-1 flex items-center gap-1" x-show="fechaFueraDeTiempo" x-cloak>
                            <x-heroicon-o-exclamation-triangle class="w-4 h-4 shrink-0" />
                            <span>Advertencia: la fecha elegida está antes del mínimo recomendado (<span x-text="fechaMinima"></span>). Verifica que la logística sea viable.</span>
                        </p>
                        @error('FECHA_PRIMERA_APLICACION')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Sesiones simultáneas</span></label>
                        <select name="SESIONES_SIMULTANEAS" class="select select-bordered"
                            x-model="sesionesSimultaneas">
                            <option value="no">No</option>
                            <option value="si">Sí</option>
                        </select>
                    </div>
                    <div class="form-control" x-show="sesionesSimultaneas === 'si'" x-cloak>
                        <label class="label"><span class="label-text">Cantidad de sesiones simultáneas</span></label>
                        <input type="number" min="1" name="CANTIDAD_SIMULTANEAS" value="{{ old('CANTIDAD_SIMULTANEAS') }}"
                            class="input input-bordered @error('CANTIDAD_SIMULTANEAS') input-error @enderror" />
                        @error('CANTIDAD_SIMULTANEAS')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Cantidad USB</span></label>
                        <input type="number" min="0" name="CANTIDAD_USB" value="{{ old('CANTIDAD_USB', 0) }}"
                            class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Cantidad CD</span></label>
                        <input type="number" min="0" name="CANTIDAD_CD" value="{{ old('CANTIDAD_CD', 0) }}"
                            class="input input-bordered" />
                    </div>
                </div>

                <div class="form-control mb-6">
                    <label class="label"><span class="label-text">Dirección de envío</span></label>
                    <textarea name="DIRECCION_ENVIO" rows="3"
                        x-model="direccionEnvio"
                        class="textarea textarea-bordered w-full"></textarea>
                    <p class="text-xs text-base-content/40 mt-1">Se precarga automáticamente al elegir la sede; puedes editarla.</p>
                </div>

                <div class="form-control mb-6">
                    <label class="label"><span class="label-text">Observaciones / Notas</span></label>
                    <textarea name="OBSERVACIONES" rows="3"
                        class="textarea textarea-bordered w-full">{{ old('OBSERVACIONES') }}</textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">Crear solicitud</button>
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-ghost">Cancelar</a>
                </div>
                <input type="hidden" name="ENVIO_ZONA" x-model="envioZona" />
            </form>
        </div>
    </div>

    {{-- Modal nueva empresa --}}
    <dialog id="modal-nueva-empresa" class="modal">
        <div class="modal-box max-w-md">
            <h3 class="font-bold text-lg mb-4">Nueva empresa</h3>
            <div class="form-control mb-3">
                <label class="label"><span class="label-text">Nombre *</span></label>
                <input type="text" id="ne-nombre" class="input input-bordered input-sm" />
            </div>
            <div class="form-control mb-3">
                <label class="label"><span class="label-text">Razón social</span></label>
                <input type="text" id="ne-razon" class="input input-bordered input-sm" />
            </div>
            <div class="form-control mb-4">
                <label class="label"><span class="label-text">RFC</span></label>
                <input type="text" id="ne-rfc" class="input input-bordered input-sm" />
            </div>
            <div class="form-control mb-4">
                <label class="label"><span class="label-text">Tipo de cliente *</span></label>
                <select id="ne-tipo-cliente" class="select select-bordered select-sm">
                    <option value="corporativo">Corporativo</option>
                    <option value="academico">Académico</option>
                    <option value="gobierno">Gobierno</option>
                </select>
            </div>
            <div class="modal-action">
                <button onclick="guardarEmpresa()" class="btn btn-primary btn-sm">Guardar</button>
                <form method="dialog"><button class="btn btn-ghost btn-sm">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- Modal nueva sede --}}
    <dialog id="modal-nueva-sede" class="modal">
        <div class="modal-box max-w-md">
            <h3 class="font-bold text-lg mb-4">Nueva sede</h3>
            <div class="form-control mb-3">
                <label class="label"><span class="label-text">Nombre *</span></label>
                <input type="text" id="ns-nombre" class="input input-bordered input-sm" />
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="form-control">
                    <label class="label"><span class="label-text">Calle y número</span></label>
                    <input type="text" id="ns-calle" class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Colonia/Barrio</span></label>
                    <input type="text" id="ns-colonia" class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Alcaldía/Municipio</span></label>
                    <input type="text" id="ns-alcaldia" class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Ciudad</span></label>
                    <input type="text" id="ns-ciudad" class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Estado</span></label>
                    <input type="text" id="ns-estado" class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Código postal</span></label>
                    <input type="text" id="ns-cp" class="input input-bordered input-sm" />
                </div>
            </div>
            <div class="modal-action">
                <button onclick="guardarSede()" class="btn btn-primary btn-sm">Guardar</button>
                <form method="dialog"><button class="btn btn-ghost btn-sm">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- Modal nuevo contacto --}}
    <dialog id="modal-nuevo-contacto" class="modal">
        <div class="modal-box max-w-md">
            <h3 class="font-bold text-lg mb-4">Nuevo contacto</h3>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Nombre *</span></label>
                    <input type="text" id="nc-nombre" class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Apellidos *</span></label>
                    <input type="text" id="nc-apellidos" class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Teléfono</span></label>
                    <input type="text" id="nc-telefono" class="input input-bordered input-sm" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Correo</span></label>
                    <input type="email" id="nc-correo" class="input input-bordered input-sm" />
                </div>
            </div>
            <div class="modal-action">
                <button onclick="guardarContacto()" class="btn btn-primary btn-sm">Guardar</button>
                <form method="dialog"><button class="btn btn-ghost btn-sm">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

<script>
    const csrfToken     = '{{ csrf_token() }}';
    const urlSedes      = '{{ url("admin/solicitudes/empresa") }}';
    const urlContactos  = '{{ url("admin/solicitudes/sede") }}';
    const urlEmpresas   = '{{ route("admin.empresas.store") }}';
    const urlSedesStore = '{{ url("admin/empresas") }}';

    function solicitudForm() {
        return {
            empresaId: '',
            sedeId: '',
            sedes: [],
            contactos: [],
            direccionEnvio: '',
            sesionesSimultaneas: 'no',
            envioZona: '',
            fechaMinima: '',
            fechaPrimeraAplicacion: '',
            get fechaFueraDeTiempo() {
                return !!this.fechaPrimeraAplicacion
                    && !!this.fechaMinima
                    && this.fechaPrimeraAplicacion < this.fechaMinima;
            },
            responsable: {
                titulo: '',
                nombre: '',
                correo: '',
                telefono: '',
            },
            examenes: [
                { uid: crypto.randomUUID(), tipo_examen_id: '', cantidad: 1 },
            ],

            async init() {
                const params = new URLSearchParams(location.search);
                const empresaId  = params.get('nueva_empresa') || params.get('empresa');
                const sedeId     = params.get('nueva_sede') || params.get('sede');
                const contactoId = params.get('nuevo_contacto');

                if (!empresaId && !sedeId && !contactoId) return;

                if (empresaId) {
                    document.querySelector('[name="ID_EMPRESA"]').value = empresaId;
                    await this.onEmpresaChange(empresaId);
                    await this.$nextTick();
                }

                if (sedeId) {
                    document.querySelector('[name="ID_SEDE"]').value = sedeId;
                    await this.onSedeChange(sedeId);
                    await this.$nextTick();
                }

                if (contactoId) {
                    document.querySelector('[name="ID_CONTACTO"]').value = contactoId;
                    this.onContactoChange(contactoId);
                }

                history.replaceState(null, '', location.pathname);
            },

            agregarExamenFila() {
                this.examenes.push({ uid: crypto.randomUUID(), tipo_examen_id: '', cantidad: 1 });
            },

            quitarExamenFila(index) {
                this.examenes.splice(index, 1);
            },

            calcularZona(estadoRepublica) {
                const estadosMetropolitanos = ['ciudad de méxico', 'cdmx', 'estado de méxico', 'edomex', 'méxico'];
                const normalizado = (estadoRepublica || '').toLowerCase().trim();
                return estadosMetropolitanos.some(e => normalizado.includes(e))
                    ? 'cdmx_area_metropolitana'
                    : 'foraneo';
            },

            actualizarFechaMinima() {
                const dias = this.envioZona === 'cdmx_area_metropolitana' ? 10 : 15;
                const fecha = new Date();
                fecha.setDate(fecha.getDate() + dias);
                this.fechaMinima = fecha.toISOString().split('T')[0];
            },

            async onEmpresaChange(id) {
                this.empresaId = id;
                this.sedes = [];
                this.contactos = [];
                this.sedeId = '';
                this.direccionEnvio = '';
                this.responsable = { titulo: '', nombre: '', correo: '', telefono: '' };
                if (!id) return;
                const res = await fetch(`${urlSedes}/${id}/sedes`);
                this.sedes = await res.json();
            },

            async onSedeChange(id) {
                this.sedeId = id;
                this.contactos = [];
                this.responsable = { titulo: '', nombre: '', correo: '', telefono: '' };
                if (!id) { this.direccionEnvio = ''; return; }

                const sede = this.sedes.find(s => s.id == id);
                if (sede) {
                    this.direccionEnvio = [
                        sede.calle_y_numero,
                        sede.colonia_barrio,
                        sede.alcaldia_municipio,
                        sede.ciudad,
                        sede.estado_republica,
                        sede.codigo_postal,
                    ].filter(Boolean).join(', ');

                    this.envioZona = this.calcularZona(sede.estado_republica);
                    this.actualizarFechaMinima();
                }

                const res = await fetch(`${urlContactos}/${id}/contactos`);
                this.contactos = await res.json();
            },

            onContactoChange(id) {
                const contacto = this.contactos.find(c => c.id == id);
                if (!contacto) {
                    this.responsable = { titulo: '', nombre: '', correo: '', telefono: '' };
                    return;
                }
                this.responsable = {
                    titulo:   '',
                    nombre:   contacto.nombre + ' ' + contacto.apellidos,
                    correo:   contacto.correo   ?? '',
                    telefono: contacto.telefono ?? '',
                };
            },
        }
    }

    // Guardar empresa rápida
    async function guardarEmpresa() {
        const nombre = document.getElementById('ne-nombre').value.trim();
        if (!nombre) { alert('El nombre es requerido.'); return; }

        const res = await fetch(urlEmpresas, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                nombre,
                razon_social: document.getElementById('ne-razon').value,
                rfc: document.getElementById('ne-rfc').value,
                tipo_cliente: document.getElementById('ne-tipo-cliente').value,
                estado: 'activo',
            }),
        });

        if (res.ok) {
            const data = await res.json();
            document.getElementById('modal-nueva-empresa').close();
            location.href = location.pathname + '?nueva_empresa=' + data.empresa.id;
        }
    }

    // Guardar sede rápida
    async function guardarSede() {
        const empresaId = document.querySelector('[name="ID_EMPRESA"]').value;
        const nombre = document.getElementById('ns-nombre').value.trim();
        if (!empresaId || !nombre) { alert('Selecciona una empresa y escribe el nombre.'); return; }

        const res = await fetch(`${urlSedesStore}/${empresaId}/sedes`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                nombre,
                calle_y_numero: document.getElementById('ns-calle').value,
                colonia_barrio: document.getElementById('ns-colonia').value,
                alcaldia_municipio: document.getElementById('ns-alcaldia').value,
                ciudad: document.getElementById('ns-ciudad').value,
                estado_republica: document.getElementById('ns-estado').value,
                codigo_postal: document.getElementById('ns-cp').value,
                estado: 'activo',
            }),
        });

        if (res.ok) {
            const data = await res.json();
            document.getElementById('modal-nueva-sede').close();
            location.href = location.pathname + `?empresa=${empresaId}&nueva_sede=${data.sede.id}`;
        }
    }

    // Guardar contacto rápido
    async function guardarContacto() {
        const empresaId = document.querySelector('[name="ID_EMPRESA"]').value;
        const sedeId = document.querySelector('[name="ID_SEDE"]').value;
        const nombre = document.getElementById('nc-nombre').value.trim();
        const apellidos = document.getElementById('nc-apellidos').value.trim();
        if (!sedeId || !nombre || !apellidos) { alert('Selecciona una sede y completa nombre y apellidos.'); return; }

        const res = await fetch(`{{ url('admin/empresas') }}/${empresaId}/contactos`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                nombre,
                apellidos,
                telefono: document.getElementById('nc-telefono').value,
                correo: document.getElementById('nc-correo').value,
                sedes: [sedeId],
            }),
        });

        if (res.ok) {
            const data = await res.json();
            document.getElementById('modal-nuevo-contacto').close();
            location.href = location.pathname + `?empresa=${empresaId}&sede=${sedeId}&nuevo_contacto=${data.contacto.id}`;
        }
    }
</script>
</x-app-layout>