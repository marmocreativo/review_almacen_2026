<!DOCTYPE html>
<html lang="es" data-theme="rq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Solicitud — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen p-4">
    <div class="max-w-2xl mx-auto py-6">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('portal.solicitudes.historial') }}" class="btn btn-ghost btn-sm btn-square">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
            </a>
            <h2 class="text-xl font-semibold">Nueva solicitud</h2>
        </div>
        <p class="text-sm text-base-content/50 mb-6">{{ $contacto->nombre }} {{ $contacto->apellidos }} — {{ $contacto->empresa?->nombre }}</p>

        @if($errors->any())
            <div class="alert alert-error text-sm mb-4"><span>{{ $errors->first() }}</span></div>
        @endif

        <div class="card bg-base-100 shadow" x-data="portalSolicitudForm()">
            <div class="card-body">
                <form method="POST" action="{{ route('portal.solicitudes.store') }}">
                    @csrf

                    {{-- Sede --}}
                    <h3 class="font-semibold text-base mb-3">Destino</h3>
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text">Sede *</span></label>
                        <select name="ID_SEDE" x-model="sedeId"
                            class="select select-bordered @error('ID_SEDE') select-error @enderror"
                            :class="{ 'select-error': sedeTocado && !sedeId }"
                            @change="onSedeChange($event.target.value)" @blur="sedeTocado = true" required>
                            <option value="">Selecciona…</option>
                            @foreach($contacto->sedes as $sede)
                                <option value="{{ $sede->id }}"
                                    data-calle="{{ $sede->calle_y_numero }}"
                                    data-colonia="{{ $sede->colonia_barrio }}"
                                    data-alcaldia="{{ $sede->alcaldia_municipio }}"
                                    data-ciudad="{{ $sede->ciudad }}"
                                    data-estado="{{ $sede->estado_republica }}"
                                    data-cp="{{ $sede->codigo_postal }}">
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('ID_SEDE')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                        <p class="text-error text-xs mt-1" x-show="sedeTocado && !sedeId">Selecciona una sede.</p>
                    </div>
                    <input type="hidden" name="ENVIO_ZONA" x-model="envioZona" />

                    <div class="divider"></div>

                    {{-- Responsable --}}
                    <h3 class="font-semibold text-base mb-3">Responsable</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Título</span></label>
                            <input type="text" name="RESPONSABLE_TITULO" value="{{ old('RESPONSABLE_TITULO') }}"
                                placeholder="Ej: Coordinador académico"
                                class="input input-bordered" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Nombre *</span></label>
                            <input type="text" name="RESPONSABLE_NOMBRE" x-model="responsableNombre"
                                @blur="responsableNombreTocado = true"
                                value="{{ old('RESPONSABLE_NOMBRE', $contacto->nombre . ' ' . $contacto->apellidos) }}"
                                class="input input-bordered @error('RESPONSABLE_NOMBRE') input-error @enderror"
                                :class="{ 'input-error': responsableNombreTocado && !responsableNombre.trim() }" required />
                            <p class="text-error text-xs mt-1" x-show="responsableNombreTocado && !responsableNombre.trim()">
                                El nombre es requerido.
                            </p>
                            @error('RESPONSABLE_NOMBRE')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Correo</span></label>
                            <input type="email" name="RESPONSABLE_CORREO" value="{{ old('RESPONSABLE_CORREO', $contacto->correo) }}"
                                class="input input-bordered" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Teléfono</span></label>
                            <input type="text" name="RESPONSABLE_TELEFONO" value="{{ old('RESPONSABLE_TELEFONO', $contacto->telefono) }}"
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
                                    <label class="label py-1"><span class="label-text text-xs">Examen *</span></label>
                                    <select :name="`examenes[${index}][tipo_examen_id]`"
                                        x-model="fila.tipo_examen_id"
                                        @change="validarFila(fila)"
                                        class="select select-bordered select-sm w-full"
                                        :class="{ 'select-error': fila.tocado && fila.errorTipo }">
                                        <option value="">Seleccionar...</option>
                                        @foreach($tipoExamenes as $te)
                                            <option value="{{ $te->id }}"
                                                data-dias="{{ $te->dias_anticipacion }}"
                                                data-min="{{ $te->candidatos_minimos }}">
                                                {{ $te->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-error text-xs mt-1" x-show="fila.tocado && fila.errorTipo" x-text="fila.errorTipo"></p>
                                </div>
                                <div class="form-control">
                                    <label class="label py-1"><span class="label-text text-xs">Cantidad sesiones *</span></label>
                                    <input type="number" min="1" :name="`examenes[${index}][cantidad]`"
                                        x-model.number="fila.cantidad"
                                        @input="validarFila(fila)" @blur="fila.tocado = true"
                                        class="input input-bordered input-sm w-full"
                                        :class="{ 'input-error': fila.tocado && fila.errorCantidad }" />
                                    <p class="text-error text-xs mt-1" x-show="fila.tocado && fila.errorCantidad" x-text="fila.errorCantidad"></p>
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
                                :min="fechaMinimaEfectiva"
                                x-model="fechaPrimeraAplicacion"
                                @blur="fechaTocada = true"
                                class="input input-bordered @error('FECHA_PRIMERA_APLICACION') input-error @enderror"
                                :class="{ 'input-error': fechaTocada && errorFecha }" />
                            <p class="text-xs text-base-content/40 mt-1" x-show="fechaMinimaEfectiva" x-text="'Fecha mínima permitida: ' + fechaMinimaEfectiva"></p>
                            <p class="text-error text-xs mt-1" x-show="fechaTocada && errorFecha" x-text="errorFecha"></p>
                            @error('FECHA_PRIMERA_APLICACION')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Sesiones simultáneas</span></label>
                            <select name="SESIONES_SIMULTANEAS" class="select select-bordered" x-model="sesionesSimultaneas">
                                <option value="no">No</option>
                                <option value="si">Sí</option>
                            </select>
                        </div>
                        <div class="form-control" x-show="sesionesSimultaneas === 'si'" x-cloak>
                            <label class="label"><span class="label-text">Cantidad de sesiones simultáneas</span></label>
                            <input type="number" min="1" name="CANTIDAD_SIMULTANEAS" value="{{ old('CANTIDAD_SIMULTANEAS') }}"
                                class="input input-bordered" />
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

                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text">Dirección de envío</span></label>
                        <textarea name="DIRECCION_ENVIO" rows="2" x-model="direccionEnvio"
                            class="textarea textarea-bordered w-full"></textarea>
                        <p class="text-xs text-base-content/40 mt-1">Se precarga automáticamente al elegir la sede; puedes editarla.</p>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label"><span class="label-text">Observaciones</span></label>
                        <textarea name="OBSERVACIONES" rows="2" class="textarea textarea-bordered w-full">{{ old('OBSERVACIONES') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full" :disabled="!formValido">
                        Enviar solicitud
                    </button>
                    <p class="text-xs text-warning text-center mt-2" x-show="!formValido">
                        Completa correctamente los campos marcados arriba para poder enviar.
                    </p>
                </form>
            </div>
        </div>
    </div>

<script>
    function portalSolicitudForm() {
        return {
            sedeId: '',
            sedeTocado: false,
            envioZona: '',
            direccionEnvio: '',
            fechaMinima: '',
            fechaPrimeraAplicacion: '',
            fechaTocada: false,
            sesionesSimultaneas: 'no',
            responsableNombre: '{{ addslashes(old('RESPONSABLE_NOMBRE', $contacto->nombre . ' ' . $contacto->apellidos)) }}',
            responsableNombreTocado: false,
            examenes: [
                { uid: crypto.randomUUID(), tipo_examen_id: '', cantidad: 1, tocado: false, errorTipo: '', errorCantidad: '', diasAnticipacion: 0, candidatosMinimos: 1 },
            ],

            get fechaMinimaEfectiva() {
                // La fecha mínima real es la mayor entre la de zona de envío y la que pida el examen más exigente.
                const diasExamenes = this.examenes
                    .map(f => f.diasAnticipacion || 0)
                    .reduce((max, dias) => Math.max(max, dias), 0);

                const diasZona = this.envioZona === 'cdmx_area_metropolitana' ? 10 : (this.envioZona === 'foraneo' ? 15 : 0);
                const dias = Math.max(diasZona, diasExamenes);

                if (!dias) return this.fechaMinima;

                const fecha = new Date();
                fecha.setDate(fecha.getDate() + dias);
                return fecha.toISOString().split('T')[0];
            },

            get errorFecha() {
                if (!this.fechaPrimeraAplicacion) return 'La fecha es requerida.';
                if (this.fechaMinimaEfectiva && this.fechaPrimeraAplicacion < this.fechaMinimaEfectiva) {
                    return `La fecha mínima permitida es ${this.fechaMinimaEfectiva}.`;
                }
                return '';
            },

            get formValido() {
                const examenesValidos = this.examenes.length > 0 &&
                    this.examenes.every(f => f.tipo_examen_id && f.cantidad >= 1 && !f.errorTipo && !f.errorCantidad);

                return this.sedeId
                    && this.responsableNombre.trim()
                    && this.fechaPrimeraAplicacion
                    && !this.errorFecha
                    && examenesValidos;
            },

            agregarExamenFila() {
                this.examenes.push({ uid: crypto.randomUUID(), tipo_examen_id: '', cantidad: 1, tocado: false, errorTipo: '', errorCantidad: '', diasAnticipacion: 0, candidatosMinimos: 1 });
            },

            quitarExamenFila(index) {
                this.examenes.splice(index, 1);
            },

            validarFila(fila) {
                fila.tocado = true;

                if (!fila.tipo_examen_id) {
                    fila.errorTipo = 'Selecciona un tipo de examen.';
                    fila.errorCantidad = '';
                    fila.diasAnticipacion = 0;
                    fila.candidatosMinimos = 1;
                    return;
                }

                const option = document.querySelector(`option[value="${fila.tipo_examen_id}"]`);
                fila.diasAnticipacion = option ? parseInt(option.dataset.dias || '0', 10) : 0;
                fila.candidatosMinimos = option ? parseInt(option.dataset.min || '1', 10) : 1;
                fila.errorTipo = '';

                if (!fila.cantidad || fila.cantidad < 1) {
                    fila.errorCantidad = 'La cantidad debe ser mayor a 0.';
                } else if (fila.cantidad < fila.candidatosMinimos) {
                    fila.errorCantidad = `La cantidad mínima para este examen es ${fila.candidatosMinimos}.`;
                } else {
                    fila.errorCantidad = '';
                }
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

                if (this.fechaPrimeraAplicacion && this.fechaPrimeraAplicacion < this.fechaMinimaEfectiva) {
                    this.fechaPrimeraAplicacion = this.fechaMinimaEfectiva;
                }
            },

            onSedeChange(id) {
                this.sedeId = id;
                this.sedeTocado = true;

                const select = document.querySelector('[name="ID_SEDE"]');
                const option = select.querySelector(`option[value="${id}"]`);
                if (!option) { this.direccionEnvio = ''; return; }

                this.direccionEnvio = [
                    option.dataset.calle,
                    option.dataset.colonia,
                    option.dataset.alcaldia,
                    option.dataset.ciudad,
                    option.dataset.estado,
                    option.dataset.cp,
                ].filter(Boolean).join(', ');

                this.envioZona = this.calcularZona(option.dataset.estado);
                this.actualizarFechaMinima();
            },
        }
    }
</script>
</body>
</html>