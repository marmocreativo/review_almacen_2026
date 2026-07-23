<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <h2 class="text-xl font-semibold">Solicitud #{{ $solicitud->ID_SOLICITUD }}</h2>
                @php
                    $badge = match($solicitud->ESTADO_SOLICITUD) {
                        'pendiente' => 'badge-warning',
                        'enviada'   => 'badge-info',
                        'retornada' => 'badge-success',
                        default     => 'badge-ghost',
                    };
                @endphp
                <span class="badge {{ $badge }}">{{ ucfirst($solicitud->ESTADO_SOLICITUD) }}</span>
            </div>
        </div>
    </x-slot>

    <x-alert />

    {{-- Navegación de pestañas --}}
    <div role="tablist" class="tabs tabs-boxed mb-4">
        <a href="{{ route('admin.solicitudes.show', $solicitud) }}" role="tab" class="tab tab-active">Datos</a>
        <a href="{{ route('admin.solicitudes.envio.show', $solicitud) }}" role="tab" class="tab">Envío</a>
        <a href="{{ route('admin.solicitudes.devolucion.show', $solicitud) }}" role="tab" class="tab">Devolución</a>
        <a href="{{ route('admin.solicitudes.facturacion.show', $solicitud) }}" role="tab" class="tab">Facturación</a>
    </div>

    <div x-data="{ editando: false }" class="card bg-base-100 shadow max-w-3xl">
        <div class="card-body">

            <div class="flex items-center justify-between mb-4">
                <h3 class="card-title text-base">Datos generales</h3>
                <button type="button" class="btn btn-outline btn-info btn-sm" x-show="!editando" @click="editando = true">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    Editar
                </button>
            </div>

            {{-- ───── SOLO LECTURA ───── --}}
            <dl x-show="!editando" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs text-base-content/50">Cliente</dt>
                    <dd class="font-medium">{{ $solicitud->empresa?->nombre ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Sede</dt>
                    <dd class="font-medium">{{ $solicitud->sede?->nombre ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Contacto</dt>
                    <dd class="font-medium">{{ $solicitud->contacto?->nombre }} {{ $solicitud->contacto?->apellidos }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Zona de envío</dt>
                    <dd class="font-medium">
                        {{ $solicitud->ENVIO_ZONA === 'cdmx_area_metropolitana' ? 'CDMX / Área Metropolitana' : ($solicitud->ENVIO_ZONA === 'foraneo' ? 'Foráneo' : '—') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Responsable</dt>
                    <dd class="font-medium">{{ $solicitud->RESPONSABLE_NOMBRE }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Correo</dt>
                    <dd class="font-medium">{{ $solicitud->RESPONSABLE_CORREO ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Teléfono / Celular</dt>
                    <dd class="font-medium">{{ $solicitud->RESPONSABLE_TELEFONO ?: '—' }} / {{ $solicitud->RESPONSABLE_CELULAR ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Sesiones simultáneas</dt>
                    <dd class="font-medium">{{ ucfirst($solicitud->SESIONES_SIMULTANEAS) }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-base-content/50">Dirección de envío</dt>
                    <dd class="font-medium">{{ $solicitud->DIRECCION_ENVIO ?: '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-base-content/50">Horario de atención</dt>
                    <dd class="font-medium">{{ $solicitud->HORARIO_DE_ATENCION ?: '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-base-content/50">Observaciones</dt>
                    <dd class="font-medium">{{ $solicitud->OBSERVACIONES ?: '—' }}</dd>
                </div>
            </dl>

            {{-- ───── FORMULARIO DE EDICIÓN ───── --}}
            <form x-show="editando" method="POST" action="{{ route('admin.solicitudes.datos.update', $solicitud) }}"
                x-data="solicitudDatosForm()" x-init="init()">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Cliente *</span></label>
                        <select name="ID_EMPRESA" x-model="empresaId" @change="cargarSedes()"
                            class="select select-bordered @error('ID_EMPRESA') select-error @enderror">
                            <option value="">Selecciona…</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        @error('ID_EMPRESA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Sede *</span></label>
                        <select name="ID_SEDE" x-model="sedeId" @change="cargarContactos()"
                            class="select select-bordered @error('ID_SEDE') select-error @enderror">
                            <option value="">Selecciona un cliente primero…</option>
                            <template x-for="sede in sedes" :key="sede.id">
                                <option :value="sede.id" x-text="sede.nombre"></option>
                            </template>
                        </select>
                        @error('ID_SEDE')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Contacto *</span></label>
                        <select name="ID_CONTACTO" x-model="contactoId" @change="autoLlenar()"
                            class="select select-bordered @error('ID_CONTACTO') select-error @enderror">
                            <option value="">Selecciona una sede primero…</option>
                            <template x-for="contacto in contactos" :key="contacto.id">
                                <option :value="contacto.id" x-text="contacto.nombre + ' ' + contacto.apellidos"></option>
                            </template>
                        </select>
                        @error('ID_CONTACTO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Zona de envío</span></label>
                        <select name="ENVIO_ZONA" class="select select-bordered @error('ENVIO_ZONA') select-error @enderror">
                            <option value="">Selecciona…</option>
                            <option value="cdmx_area_metropolitana" {{ old('ENVIO_ZONA', $solicitud->ENVIO_ZONA) === 'cdmx_area_metropolitana' ? 'selected' : '' }}>CDMX / Área Metropolitana</option>
                            <option value="foraneo" {{ old('ENVIO_ZONA', $solicitud->ENVIO_ZONA) === 'foraneo' ? 'selected' : '' }}>Foráneo</option>
                        </select>
                        @error('ENVIO_ZONA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Nombre del responsable *</span></label>
                        <input type="text" name="RESPONSABLE_NOMBRE" x-model="form.nombre"
                            class="input input-bordered @error('RESPONSABLE_NOMBRE') input-error @enderror" />
                        @error('RESPONSABLE_NOMBRE')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Correo</span></label>
                        <input type="email" name="RESPONSABLE_CORREO" x-model="form.correo"
                            class="input input-bordered @error('RESPONSABLE_CORREO') input-error @enderror" />
                        @error('RESPONSABLE_CORREO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Teléfono</span></label>
                        <input type="text" name="RESPONSABLE_TELEFONO" x-model="form.telefono"
                            class="input input-bordered @error('RESPONSABLE_TELEFONO') input-error @enderror" />
                        @error('RESPONSABLE_TELEFONO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Celular</span></label>
                        <input type="text" name="RESPONSABLE_CELULAR" value="{{ old('RESPONSABLE_CELULAR', $solicitud->RESPONSABLE_CELULAR) }}"
                            class="input input-bordered @error('RESPONSABLE_CELULAR') input-error @enderror" />
                        @error('RESPONSABLE_CELULAR')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Sesiones simultáneas *</span></label>
                        <select name="SESIONES_SIMULTANEAS" class="select select-bordered @error('SESIONES_SIMULTANEAS') select-error @enderror">
                            <option value="no" {{ old('SESIONES_SIMULTANEAS', $solicitud->SESIONES_SIMULTANEAS) === 'no' ? 'selected' : '' }}>No</option>
                            <option value="si" {{ old('SESIONES_SIMULTANEAS', $solicitud->SESIONES_SIMULTANEAS) === 'si' ? 'selected' : '' }}>Sí</option>
                        </select>
                        @error('SESIONES_SIMULTANEAS')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text">Dirección de envío</span></label>
                        <textarea name="DIRECCION_ENVIO" rows="2"
                            class="textarea textarea-bordered @error('DIRECCION_ENVIO') textarea-error @enderror">{{ old('DIRECCION_ENVIO', $solicitud->DIRECCION_ENVIO) }}</textarea>
                        @error('DIRECCION_ENVIO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text">Horario de atención</span></label>
                        <textarea name="HORARIO_DE_ATENCION" rows="2"
                            class="textarea textarea-bordered @error('HORARIO_DE_ATENCION') textarea-error @enderror">{{ old('HORARIO_DE_ATENCION', $solicitud->HORARIO_DE_ATENCION) }}</textarea>
                        @error('HORARIO_DE_ATENCION')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text">Observaciones</span></label>
                        <textarea name="OBSERVACIONES" rows="2"
                            class="textarea textarea-bordered @error('OBSERVACIONES') textarea-error @enderror">{{ old('OBSERVACIONES', $solicitud->OBSERVACIONES) }}</textarea>
                        @error('OBSERVACIONES')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <button type="button" class="btn btn-ghost" @click="editando = false">Cancelar</button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function solicitudDatosForm() {
            return {
                empresaId: '{{ $solicitud->ID_EMPRESA }}',
                sedeId: '{{ $solicitud->ID_SEDE }}',
                contactoId: '{{ $solicitud->ID_CONTACTO }}',
                sedes: [],
                contactos: [],
                form: {
                    nombre: '{{ addslashes($solicitud->RESPONSABLE_NOMBRE) }}',
                    correo: '{{ addslashes($solicitud->RESPONSABLE_CORREO) }}',
                    telefono: '{{ addslashes($solicitud->RESPONSABLE_TELEFONO) }}',
                },

                async init() {
                    if (this.empresaId) await this.cargarSedes(true);
                    if (this.sedeId) await this.cargarContactos(true);
                },

                async cargarSedes(preservarSeleccion = false) {
                    if (!this.empresaId) { this.sedes = []; this.contactos = []; return; }
                    const res = await fetch(`/admin/solicitudes/empresa/${this.empresaId}/sedes`);
                    this.sedes = await res.json();
                    if (!preservarSeleccion) {
                        this.sedeId = '';
                        this.contactoId = '';
                        this.contactos = [];
                    }
                },

                async cargarContactos(preservarSeleccion = false) {
                    if (!this.sedeId) { this.contactos = []; return; }
                    const res = await fetch(`/admin/solicitudes/sede/${this.sedeId}/contactos`);
                    this.contactos = await res.json();
                    if (!preservarSeleccion) {
                        this.contactoId = '';
                    }
                },

                autoLlenar() {
                    const contacto = this.contactos.find(c => c.id == this.contactoId);
                    if (contacto) {
                        this.form.nombre = contacto.nombre + ' ' + contacto.apellidos;
                        this.form.correo = contacto.correo ?? '';
                        this.form.telefono = contacto.telefono ?? '';
                    }
                }
            }
        }
    </script>
</x-app-layout>