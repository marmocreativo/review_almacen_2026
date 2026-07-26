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
                    <dd class="font-medium">{{ trim(($solicitud->RESPONSABLE_TITULO ? $solicitud->RESPONSABLE_TITULO . ' ' : '') . $solicitud->RESPONSABLE_NOMBRE) }}</dd>
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
                    <dd class="font-medium">
                        {{ ucfirst($solicitud->SESIONES_SIMULTANEAS) }}
                        @if($solicitud->SESIONES_SIMULTANEAS === 'si' && $solicitud->CANTIDAD_SIMULTANEAS)
                            ({{ $solicitud->CANTIDAD_SIMULTANEAS }})
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Fecha primer aplicación</dt>
                    <dd class="font-medium">{{ $solicitud->FECHA_PRIMERA_APLICACION?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Cantidad USB / CD</dt>
                    <dd class="font-medium">{{ $solicitud->CANTIDAD_USB ?? 0 }} / {{ $solicitud->CANTIDAD_CD ?? 0 }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Aprobación</dt>
                    <dd>
                        @php
                            $badgeAprob = match($solicitud->APROBACION) {
                                'aprobada'  => 'badge-success',
                                'cancelada' => 'badge-error',
                                default     => 'badge-warning',
                            };
                        @endphp
                        <span class="badge badge-sm {{ $badgeAprob }}">{{ ucfirst($solicitud->APROBACION ?? 'pendiente') }}</span>
                    </dd>
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
                x-data="solicitudDatosForm()">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Cliente</span></label>
                        <input type="text" value="{{ $solicitud->empresa?->nombre ?? '—' }}" disabled class="input input-bordered input-disabled" />
                        <input type="hidden" name="ID_EMPRESA" value="{{ $solicitud->ID_EMPRESA }}" />
                        <p class="text-xs text-base-content/40 mt-1">No editable desde aquí.</p>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Sede</span></label>
                        <input type="text" value="{{ $solicitud->sede?->nombre ?? '—' }}" disabled class="input input-bordered input-disabled" />
                        <input type="hidden" name="ID_SEDE" value="{{ $solicitud->ID_SEDE }}" />
                        <p class="text-xs text-base-content/40 mt-1">No editable desde aquí.</p>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Contacto</span></label>
                        <input type="text" value="{{ $solicitud->contacto ? $solicitud->contacto->nombre . ' ' . $solicitud->contacto->apellidos : '—' }}" disabled class="input input-bordered input-disabled" />
                        <input type="hidden" name="ID_CONTACTO" value="{{ $solicitud->ID_CONTACTO }}" />
                        <p class="text-xs text-base-content/40 mt-1">No editable desde aquí.</p>
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
                        <label class="label"><span class="label-text">Título</span></label>
                        <input type="text" name="RESPONSABLE_TITULO" value="{{ old('RESPONSABLE_TITULO', $solicitud->RESPONSABLE_TITULO) }}"
                            class="input input-bordered @error('RESPONSABLE_TITULO') input-error @enderror" />
                        @error('RESPONSABLE_TITULO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
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

                    <div class="contents" x-data="{ sesionesSim: '{{ old('SESIONES_SIMULTANEAS', $solicitud->SESIONES_SIMULTANEAS) }}' }">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Sesiones simultáneas *</span></label>
                            <select name="SESIONES_SIMULTANEAS" x-model="sesionesSim"
                                class="select select-bordered @error('SESIONES_SIMULTANEAS') select-error @enderror">
                                <option value="no">No</option>
                                <option value="si">Sí</option>
                            </select>
                            @error('SESIONES_SIMULTANEAS')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control" x-show="sesionesSim === 'si'" x-cloak>
                            <label class="label"><span class="label-text">Cantidad de sesiones simultáneas</span></label>
                            <input type="number" min="1" name="CANTIDAD_SIMULTANEAS" value="{{ old('CANTIDAD_SIMULTANEAS', $solicitud->CANTIDAD_SIMULTANEAS) }}"
                                class="input input-bordered @error('CANTIDAD_SIMULTANEAS') input-error @enderror" />
                            @error('CANTIDAD_SIMULTANEAS')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Fecha primer aplicación *</span></label>
                        <input type="date" name="FECHA_PRIMERA_APLICACION"
                            value="{{ old('FECHA_PRIMERA_APLICACION', optional($solicitud->FECHA_PRIMERA_APLICACION)->format('Y-m-d')) }}"
                            class="input input-bordered @error('FECHA_PRIMERA_APLICACION') input-error @enderror" />
                        @error('FECHA_PRIMERA_APLICACION')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Cantidad USB</span></label>
                        <input type="number" min="0" name="CANTIDAD_USB" value="{{ old('CANTIDAD_USB', $solicitud->CANTIDAD_USB) }}"
                            class="input input-bordered @error('CANTIDAD_USB') input-error @enderror" />
                        @error('CANTIDAD_USB')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Cantidad CD</span></label>
                        <input type="number" min="0" name="CANTIDAD_CD" value="{{ old('CANTIDAD_CD', $solicitud->CANTIDAD_CD) }}"
                            class="input input-bordered @error('CANTIDAD_CD') input-error @enderror" />
                        @error('CANTIDAD_CD')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Aprobación *</span></label>
                        <select name="APROBACION" class="select select-bordered @error('APROBACION') select-error @enderror">
                            <option value="pendiente" {{ old('APROBACION', $solicitud->APROBACION) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="aprobada" {{ old('APROBACION', $solicitud->APROBACION) === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                            <option value="cancelada" {{ old('APROBACION', $solicitud->APROBACION) === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        @error('APROBACION')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    </div>

                <input type="hidden" name="HORARIO_DE_ATENCION" value="{{ old('HORARIO_DE_ATENCION', $solicitud->HORARIO_DE_ATENCION) }}" />

                <div class="form-control mt-4">
                    <label class="label"><span class="label-text">Dirección de envío</span></label>
                    <textarea name="DIRECCION_ENVIO" rows="2" class="textarea textarea-bordered w-full @error('DIRECCION_ENVIO') textarea-error @enderror">{{ old('DIRECCION_ENVIO', $solicitud->DIRECCION_ENVIO) }}</textarea>
                    @error('DIRECCION_ENVIO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mt-4">
                    <label class="label"><span class="label-text">Observaciones</span></label>
                    <textarea name="OBSERVACIONES" rows="2" class="textarea textarea-bordered w-full @error('OBSERVACIONES') textarea-error @enderror">{{ old('OBSERVACIONES', $solicitud->OBSERVACIONES) }}</textarea>
                    @error('OBSERVACIONES')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
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
                form: {
                    nombre: '{{ addslashes($solicitud->RESPONSABLE_NOMBRE) }}',
                    correo: '{{ addslashes($solicitud->RESPONSABLE_CORREO) }}',
                    telefono: '{{ addslashes($solicitud->RESPONSABLE_TELEFONO) }}',
                },
            }
        }
    </script>
</x-app-layout>