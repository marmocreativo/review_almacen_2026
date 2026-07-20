<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                    class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold truncate">
                        Solicitud <span class="font-mono text-primary">#{{ $solicitud->ID_SOLICITUD }}</span>
                    </h2>
                    <p class="text-xs text-base-content/60 truncate">
                        {{ $solicitud->empresa?->nombre }} — {{ $solicitud->sede?->nombre }}
                    </p>
                </div>
            </div>
            {{-- Estado + acciones desktop --}}
            <div class="hidden md:flex gap-2 items-center">
                @php
                    $badgeEstado = match($solicitud->ESTADO_SOLICITUD) {
                        'pendiente' => 'badge-warning',
                        'enviada'   => 'badge-info',
                        'retornada' => 'badge-success',
                        default     => 'badge-ghost',
                    };
                @endphp
                <span class="badge {{ $badgeEstado }} badge-lg">{{ ucfirst($solicitud->ESTADO_SOLICITUD) }}</span>

                @if($solicitud->isPendiente())
                    <form method="POST" action="{{ route('admin.solicitudes.estado', $solicitud->ID_SOLICITUD) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="enviada" />
                        <button type="submit" class="btn btn-info btn-sm gap-1"
                            onclick="return confirm('¿Marcar como enviada? Ya no se podrá editar.')">
                            <x-heroicon-o-paper-airplane class="w-4 h-4" />
                            Marcar como enviada
                        </button>
                    </form>
                @endif

                @if($solicitud->isEnviada())
                    <form method="POST" action="{{ route('admin.solicitudes.estado', $solicitud->ID_SOLICITUD) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="retornada" />
                        <button type="submit" class="btn btn-success btn-sm gap-1"
                            onclick="return confirm('¿Marcar como retornada?')">
                            <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                            Marcar como retornada
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- ===================== TOOLBAR MÓVIL ===================== --}}
    @php $estado = $solicitud->ESTADO_SOLICITUD; @endphp
    <div class="md:hidden bg-base-100 border-b border-base-300 px-4 py-3 flex items-center gap-3">

        {{-- Línea de progreso que ocupa todo el espacio disponible --}}
        <div class="flex items-center flex-1 min-w-0">
            <div class="flex flex-col items-center gap-1 shrink-0">
                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                    {{ in_array($estado, ['pendiente','enviada','retornada']) ? 'bg-primary border-primary' : 'border-base-300' }}">
                    @if(in_array($estado, ['enviada','retornada']))
                        <x-heroicon-s-check class="w-3 h-3 text-primary-content" />
                    @endif
                </div>
                <span class="text-xs {{ $estado === 'pendiente' ? 'text-primary font-medium' : 'text-base-content/40' }}">
                    Pend.
                </span>
            </div>

            <div class="flex-1 h-0.5 mb-4 mx-1 {{ in_array($estado, ['enviada','retornada']) ? 'bg-primary' : 'bg-base-300' }}"></div>

            <div class="flex flex-col items-center gap-1 shrink-0">
                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                    {{ in_array($estado, ['enviada','retornada']) ? 'bg-primary border-primary' : 'border-base-300' }}">
                    @if($estado === 'retornada')
                        <x-heroicon-s-check class="w-3 h-3 text-primary-content" />
                    @endif
                </div>
                <span class="text-xs {{ $estado === 'enviada' ? 'text-primary font-medium' : 'text-base-content/40' }}">
                    Env.
                </span>
            </div>

            <div class="flex-1 h-0.5 mb-4 mx-1 {{ $estado === 'retornada' ? 'bg-primary' : 'bg-base-300' }}"></div>

            <div class="flex flex-col items-center gap-1 shrink-0">
                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                    {{ $estado === 'retornada' ? 'bg-primary border-primary' : 'border-base-300' }}">
                </div>
                <span class="text-xs {{ $estado === 'retornada' ? 'text-primary font-medium' : 'text-base-content/40' }}">
                    Ret.
                </span>
            </div>
        </div>

        {{-- Botón a la derecha --}}
        @if($solicitud->isPendiente())
            <form method="POST" action="{{ route('admin.solicitudes.estado', $solicitud->ID_SOLICITUD) }}" class="shrink-0">
                @csrf @method('PATCH')
                <input type="hidden" name="estado" value="enviada" />
                <button type="submit" class="btn btn-info btn-sm gap-1"
                    onclick="return confirm('¿Marcar como enviada? Ya no se podrá editar.')">
                    <x-heroicon-o-paper-airplane class="w-4 h-4" />
                    Enviar
                </button>
            </form>
        @elseif($solicitud->isEnviada())
            <form method="POST" action="{{ route('admin.solicitudes.estado', $solicitud->ID_SOLICITUD) }}" class="shrink-0">
                @csrf @method('PATCH')
                <input type="hidden" name="estado" value="retornada" />
                <button type="submit" class="btn btn-success btn-sm gap-1"
                    onclick="return confirm('¿Marcar como retornada?')">
                    <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                    Retornar
                </button>
            </form>
        @endif
    </div>

    <x-alert />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- ===================== COLUMNA IZQUIERDA: Datos generales ===================== --}}
        <div class="xl:col-span-1">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base flex items-center gap-2">
                        <x-heroicon-o-building-office-2 class="w-5 h-5 text-primary" />
                        Datos generales
                    </h3>
                    @if($solicitud->isPendiente())
                        <form method="POST" action="{{ route('admin.solicitudes.update', $solicitud->ID_SOLICITUD) }}">
                        @csrf @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-3">

                            <div class="form-control sm:col-span-2 xl:col-span-1">
                                <label class="label"><span class="label-text text-xs">Empresa *</span></label>
                                <select name="ID_EMPRESA" id="edit-empresa"
                                    class="select select-bordered select-sm w-full"
                                    onchange="cargarSedesEdit(this.value)">
                                    <option value="">Seleccionar...</option>
                                    @foreach($empresas as $e)
                                        <option value="{{ $e->id }}"
                                            {{ $solicitud->ID_EMPRESA == $e->id ? 'selected' : '' }}>
                                            {{ $e->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-xs">Sede *</span></label>
                                <select name="ID_SEDE" id="edit-sede"
                                    class="select select-bordered select-sm w-full"
                                    onchange="cargarContactosEdit(this.value)">
                                    <option value="{{ $solicitud->ID_SEDE }}">
                                        {{ $solicitud->sede?->nombre }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-xs">Contacto *</span></label>
                                <select name="ID_CONTACTO" id="edit-contacto"
                                    class="select select-bordered select-sm w-full"
                                    onchange="onContactoChangeEdit(this.value)">
                                    <option value="{{ $solicitud->ID_CONTACTO }}">
                                        {{ $solicitud->contacto?->nombre }} {{ $solicitud->contacto?->apellidos }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-control sm:col-span-2 xl:col-span-1">
                                <label class="label"><span class="label-text text-xs">Responsable *</span></label>
                                <input type="text" name="RESPONSABLE_NOMBRE" id="edit-resp-nombre"
                                    value="{{ old('RESPONSABLE_NOMBRE', $solicitud->RESPONSABLE_NOMBRE) }}"
                                    class="input input-bordered input-sm w-full" />
                            </div>

                            <div class="form-control sm:col-span-2 xl:col-span-1">
                                <label class="label"><span class="label-text text-xs">Correo</span></label>
                                <input type="email" name="RESPONSABLE_CORREO" id="edit-resp-correo"
                                    value="{{ old('RESPONSABLE_CORREO', $solicitud->RESPONSABLE_CORREO) }}"
                                    class="input input-bordered input-sm w-full" />
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-xs">Teléfono</span></label>
                                <input type="text" name="RESPONSABLE_TELEFONO" id="edit-resp-telefono"
                                    value="{{ old('RESPONSABLE_TELEFONO', $solicitud->RESPONSABLE_TELEFONO) }}"
                                    class="input input-bordered input-sm w-full" />
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-xs">Celular</span></label>
                                <input type="text" name="RESPONSABLE_CELULAR" id="edit-resp-celular"
                                    value="{{ old('RESPONSABLE_CELULAR', $solicitud->RESPONSABLE_CELULAR) }}"
                                    class="input input-bordered input-sm w-full" />
                            </div>

                            <div class="form-control sm:col-span-2 xl:col-span-1">
                                <label class="label"><span class="label-text text-xs">Dirección de envío</span></label>
                                <textarea name="DIRECCION_ENVIO" rows="2"
                                    class="textarea textarea-bordered textarea-sm w-full">{{ old('DIRECCION_ENVIO', $solicitud->DIRECCION_ENVIO) }}</textarea>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-xs">Sesiones simultáneas</span></label>
                                <select name="SESIONES_SIMULTANEAS" class="select select-bordered select-sm w-full">
                                    <option value="no" {{ $solicitud->SESIONES_SIMULTANEAS === 'no' ? 'selected' : '' }}>No</option>
                                    <option value="si" {{ $solicitud->SESIONES_SIMULTANEAS === 'si' ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-xs">Horario de atención</span></label>
                                <input type="text" name="HORARIO_DE_ATENCION"
                                    value="{{ old('HORARIO_DE_ATENCION', $solicitud->HORARIO_DE_ATENCION) }}"
                                    placeholder="Ej: 9:00 a 18:00 hrs"
                                    class="input input-bordered input-sm w-full" />
                            </div>

                            <div class="form-control sm:col-span-2 xl:col-span-1">
                                <label class="label"><span class="label-text text-xs">Observaciones</span></label>
                                <textarea name="OBSERVACIONES" rows="2"
                                    class="textarea textarea-bordered textarea-sm w-full">{{ old('OBSERVACIONES', $solicitud->OBSERVACIONES) }}</textarea>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-full mt-4 gap-1">
                            <x-heroicon-o-check class="w-4 h-4" />
                            Actualizar datos
                        </button>
                    </form>

                    @else
                    {{-- Solo lectura --}}
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-xs text-base-content/40 uppercase tracking-wide mb-1">Destino</p>
                            <div class="space-y-1">
                                <div class="flex justify-between gap-2">
                                    <span class="text-base-content/60 shrink-0">Empresa</span>
                                    <span class="font-medium text-right">{{ $solicitud->empresa?->nombre }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-base-content/60 shrink-0">Sede</span>
                                    <span class="font-medium text-right">{{ $solicitud->sede?->nombre }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-base-content/60 shrink-0">Contacto</span>
                                    <span class="font-medium text-right">
                                        {{ $solicitud->contacto?->nombre }} {{ $solicitud->contacto?->apellidos }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="divider my-1"></div>
                        <div>
                            <p class="text-xs text-base-content/40 uppercase tracking-wide mb-1">Responsable</p>
                            <div class="space-y-1">
                                <div class="flex justify-between gap-2">
                                    <span class="text-base-content/60 shrink-0">Nombre</span>
                                    <span class="font-medium text-right">{{ $solicitud->RESPONSABLE_NOMBRE }}</span>
                                </div>
                                @if($solicitud->RESPONSABLE_CORREO)
                                    <div class="flex justify-between gap-2">
                                        <span class="text-base-content/60 shrink-0">Correo</span>
                                        <a href="mailto:{{ $solicitud->RESPONSABLE_CORREO }}"
                                            class="text-primary text-right truncate">
                                            {{ $solicitud->RESPONSABLE_CORREO }}
                                        </a>
                                    </div>
                                @endif
                                @if($solicitud->RESPONSABLE_TELEFONO)
                                    <div class="flex justify-between gap-2">
                                        <span class="text-base-content/60 shrink-0">Teléfono</span>
                                        <span class="font-medium">{{ $solicitud->RESPONSABLE_TELEFONO }}</span>
                                    </div>
                                @endif
                                @if($solicitud->RESPONSABLE_CELULAR)
                                    <div class="flex justify-between gap-2">
                                        <span class="text-base-content/60 shrink-0">Celular</span>
                                        <span class="font-medium">{{ $solicitud->RESPONSABLE_CELULAR }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="divider my-1"></div>
                        <div>
                            <p class="text-xs text-base-content/40 uppercase tracking-wide mb-1">Logística</p>
                            <div class="space-y-2">
                                <div class="flex justify-between gap-2">
                                    <span class="text-base-content/60 shrink-0">Sesiones simult.</span>
                                    <span class="badge badge-sm {{ $solicitud->SESIONES_SIMULTANEAS === 'si' ? 'badge-success' : 'badge-ghost' }}">
                                        {{ $solicitud->SESIONES_SIMULTANEAS === 'si' ? 'Sí' : 'No' }}
                                    </span>
                                </div>
                                @if($solicitud->HORARIO_DE_ATENCION)
                                    <div class="flex justify-between gap-2">
                                        <span class="text-base-content/60 shrink-0">Horario</span>
                                        <span class="font-medium text-right">{{ $solicitud->HORARIO_DE_ATENCION }}</span>
                                    </div>
                                @endif
                                @if($solicitud->DIRECCION_ENVIO)
                                    <div>
                                        <span class="text-base-content/60 block mb-1">Dirección de envío</span>
                                        <p class="bg-base-200 rounded p-2 text-xs leading-relaxed">{{ $solicitud->DIRECCION_ENVIO }}</p>
                                    </div>
                                @endif
                                @if($solicitud->OBSERVACIONES)
                                    <div>
                                        <span class="text-base-content/60 block mb-1">Observaciones</span>
                                        <p class="bg-base-200 rounded p-2 text-xs leading-relaxed">{{ $solicitud->OBSERVACIONES }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="divider my-1"></div>
                        <div class="flex justify-between gap-2">
                            <span class="text-base-content/60 shrink-0">Registro</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- ===================== COLUMNA DERECHA: Exámenes ===================== --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Agregar examen (solo pendiente) --}}
            @if($solicitud->isPendiente())
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title text-base flex items-center gap-2">
                            <x-heroicon-o-plus-circle class="w-5 h-5 text-primary" />
                            Agregar examen
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                            <div class="form-control col-span-2">
                                <label class="label"><span class="label-text text-xs">Tipo de examen *</span></label>
                                <select id="tipo-examen-select" class="select select-bordered select-sm"
                                    onchange="actualizarFechaMin()">
                                    <option value="">Seleccionar...</option>
                                    @foreach($tipoExamenes as $tipo)
                                        <option value="{{ $tipo->id }}"
                                            data-dias="{{ $tipo->dias_anticipacion }}"
                                            data-candidatos="{{ $tipo->candidatos_minimos }}">
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text text-xs">Candidatos *</span></label>
                                <input type="number" id="examen-cantidad" min="1" value="1"
                                    class="input input-bordered input-sm" />
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text text-xs">Fecha *</span>
                                    <span class="label-text-alt text-xs text-base-content/50" id="dias-anticipacion-label"></span>
                                </label>
                                <input type="date" id="examen-fecha" class="input input-bordered input-sm" />
                            </div>
                        </div>
                        <div id="examen-error" class="text-error text-sm hidden mt-1"></div>
                        <button onclick="agregarExamen()" class="btn btn-primary btn-sm mt-3 gap-1 w-full sm:w-auto">
                            <x-heroicon-o-plus class="w-4 h-4" />
                            Agregar examen
                        </button>
                    </div>
                </div>
            @endif

            {{-- Lista de exámenes --}}
            @forelse($solicitud->examenes as $examen)
                <div class="card bg-base-100 shadow" id="examen-{{ $examen->ID }}">
                    <div class="card-body">

                        {{-- Cabecera examen --}}
                        <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                            <div class="min-w-0">
                                <h3 class="font-semibold flex items-center gap-2">
                                    <x-heroicon-o-document-text class="w-4 h-4 text-primary shrink-0" />
                                    {{ $examen->EXAMEN }}
                                </h3>
                                <p class="text-sm text-base-content/60 mt-0.5">
                                    {{ $examen->CANTIDAD }} candidatos —
                                    {{ \Carbon\Carbon::parse($examen->FECHA)->format('d/m/Y') }}
                                </p>
                            </div>
                            @if($solicitud->isPendiente())
                                <button onclick="eliminarExamen({{ $examen->ID }})"
                                    class="btn btn-error btn-xs gap-1 shrink-0">
                                    <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                    Eliminar
                                </button>
                            @endif
                        </div>

                        {{-- DESKTOP: tabla --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="table table-sm table-zebra">
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Serie</th>
                                        <th>Nombre</th>
                                        <th class="text-center">Cant.</th>
                                        <th>Estado</th>
                                        @if($solicitud->isPendiente() || $solicitud->isRetornada())
                                            <th></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($examen->articulos as $item)
                                        <tr id="art-{{ $item->ID }}">
                                            <td class="font-mono text-sm">{{ $item->FOLIO }}</td>
                                            <td class="font-mono text-sm">{{ $item->SERIE ?: '—' }}</td>
                                            <td>{{ $item->NOMBRE }}</td>
                                            <td class="text-center">{{ $item->CANTIDAD_ENVIADA }}</td>
                                            <td>
                                                @if($item->ESTADO === 'retornado')
                                                    <div class="flex flex-wrap gap-1">
                                                        <span class="badge badge-success badge-sm">retornado</span>
                                                        @if($item->CANTIDAD_A_ALMACEN > 0)
                                                            <span class="badge badge-info badge-sm">almacén: {{ $item->CANTIDAD_A_ALMACEN }}</span>
                                                        @endif
                                                        @if($item->CANTIDAD_A_DESTRUCCION > 0)
                                                            <span class="badge badge-warning badge-sm">dest: {{ $item->CANTIDAD_A_DESTRUCCION }}</span>
                                                        @endif
                                                        @if($item->CANTIDAD_PERDIDOS > 0)
                                                            <span class="badge badge-error badge-sm">perd: {{ $item->CANTIDAD_PERDIDOS }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge badge-ghost badge-sm">{{ $item->ESTADO }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex gap-1">
                                                    @if($solicitud->isPendiente())
                                                        <button onclick="eliminarArticulo({{ $item->ID }}, {{ $solicitud->ID_SOLICITUD }}, {{ $examen->ID }})"
                                                            class="btn btn-error btn-xs">
                                                            <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                                        </button>
                                                    @endif
                                                    @if($solicitud->isRetornada() && !$item->isRetornado())
                                                        <button onclick="abrirRetorno({{ $item->ID }}, {{ $item->tieneSerie() ? 'true' : 'false' }}, {{ $item->CANTIDAD_ENVIADA }}, '{{ $item->FOLIO }}', '{{ $item->SERIE }}')"
                                                            class="btn btn-success btn-xs gap-1">
                                                            <x-heroicon-o-arrow-uturn-left class="w-3.5 h-3.5" />
                                                            Retornar
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-base-content/50 py-4 text-sm">
                                                Sin artículos asignados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- MÓVIL: cards por artículo --}}
                        <div class="md:hidden space-y-2">
                            @forelse($examen->articulos as $item)
                                <div class="bg-base-200/40 rounded-lg p-3" id="art-mobile-{{ $item->ID }}">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-medium text-sm truncate">{{ $item->NOMBRE }}</p>
                                            <div class="flex gap-2 mt-0.5 flex-wrap">
                                                <span class="font-mono text-xs text-base-content/50">{{ $item->FOLIO }}</span>
                                                @if($item->SERIE)
                                                    <span class="font-mono text-xs text-base-content/40">{{ $item->SERIE }}</span>
                                                @endif
                                                <span class="text-xs text-base-content/40">Cant: {{ $item->CANTIDAD_ENVIADA }}</span>
                                            </div>
                                        </div>
                                        <div class="shrink-0">
                                            @if($item->ESTADO === 'retornado')
                                                <span class="badge badge-success badge-sm">retornado</span>
                                            @else
                                                <span class="badge badge-ghost badge-sm">{{ $item->ESTADO }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($item->ESTADO === 'retornado')
                                        <div class="grid grid-cols-3 gap-1 text-center text-xs mb-2">
                                            <div class="bg-info/10 rounded p-1.5">
                                                <p class="text-info/70 leading-none mb-0.5">Almacén</p>
                                                <p class="font-mono font-bold text-info">{{ $item->CANTIDAD_A_ALMACEN }}</p>
                                            </div>
                                            <div class="bg-warning/10 rounded p-1.5">
                                                <p class="text-warning/70 leading-none mb-0.5">Dest.</p>
                                                <p class="font-mono font-bold text-warning">{{ $item->CANTIDAD_A_DESTRUCCION }}</p>
                                            </div>
                                            <div class="bg-error/10 rounded p-1.5">
                                                <p class="text-error/70 leading-none mb-0.5">Perd.</p>
                                                <p class="font-mono font-bold text-error">{{ $item->CANTIDAD_PERDIDOS }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex gap-2 mt-1">
                                        @if($solicitud->isPendiente())
                                            <button onclick="eliminarArticulo({{ $item->ID }}, {{ $solicitud->ID_SOLICITUD }}, {{ $examen->ID }})"
                                                class="btn btn-error btn-xs gap-1 flex-1">
                                                <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                                Quitar
                                            </button>
                                        @endif
                                        @if($solicitud->isRetornada() && !$item->isRetornado())
                                            <button onclick="abrirRetorno({{ $item->ID }}, {{ $item->tieneSerie() ? 'true' : 'false' }}, {{ $item->CANTIDAD_ENVIADA }}, '{{ $item->FOLIO }}', '{{ $item->SERIE }}')"
                                                class="btn btn-success btn-xs gap-1 flex-1">
                                                <x-heroicon-o-arrow-uturn-left class="w-3.5 h-3.5" />
                                                Retornar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-base-content/50 py-4 text-sm">Sin artículos asignados.</p>
                            @endforelse
                        </div>

                        {{-- Botón agregar artículos --}}
                        @if($solicitud->isPendiente())
                            <button onclick="abrirModalArticulo({{ $examen->ID }})"
                                class="btn btn-outline btn-sm mt-3 gap-1 w-full sm:w-auto">
                                <x-heroicon-o-plus class="w-4 h-4" />
                                Agregar artículo
                            </button>
                        @endif

                    </div>
                </div>
            @empty
                <div class="card bg-base-100 shadow">
                    <div class="card-body text-center text-base-content/50">
                        No hay exámenes en esta solicitud.
                    </div>
                </div>
            @endforelse

        </div>
    </div>

    {{-- ===================== MODAL ARTÍCULOS ===================== --}}
    <dialog id="modal-articulos" class="modal">
        <div class="modal-box w-11/12 max-w-2xl">
            <h3 class="font-bold text-lg mb-4">Agregar artículo al examen</h3>

            <div class="flex gap-4 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipo_busqueda_sol" value="folio" checked
                        class="radio radio-sm" onchange="tipoBusquedaSol='folio'" />
                    <span class="text-sm">Por Folio</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="tipo_busqueda_sol" value="serie"
                        class="radio radio-sm" onchange="tipoBusquedaSol='serie'" />
                    <span class="text-sm">Por Serie</span>
                </label>
            </div>

            <div class="flex gap-2 mb-4">
                <input type="text" id="sol-input-busqueda" placeholder="Escribe folio o serie..."
                    class="input input-bordered input-sm flex-1" />
                <button onclick="buscarArticuloSol()" class="btn btn-primary btn-sm gap-1">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    Buscar
                </button>
            </div>

            <div id="sol-resultado"></div>

            <div class="modal-action">
                <form method="dialog"><button class="btn btn-ghost btn-sm">Cerrar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- ===================== MODAL RETORNO ===================== --}}
    <dialog id="modal-retorno" class="modal">
        <div class="modal-box w-11/12 max-w-md">
            <h3 class="font-bold text-lg mb-1">Retornar artículo</h3>
            <p class="text-sm text-base-content/60 mb-4 bg-base-200 rounded p-2" id="retorno-descripcion"></p>

            {{-- Con serie --}}
            <div id="retorno-con-serie" class="hidden">
                <div class="form-control mb-3">
                    <label class="label"><span class="label-text text-sm font-medium">Destino *</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['almacen' => 'Almacén', 'destruccion' => 'Destrucción', 'perdido' => 'Perdido'] as $val => $label)
                            <label class="flex flex-col items-center gap-1 cursor-pointer border border-base-300 rounded-lg p-2 hover:bg-base-200 has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="destino" value="{{ $val }}" class="radio radio-sm"
                                    {{ $val === 'almacen' ? 'checked' : '' }}
                                    onchange="onDestinoChange(this.value)" />
                                <span class="text-xs">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sin serie --}}
            <div id="retorno-sin-serie" class="hidden">
                <p class="text-sm mb-3 font-medium" id="retorno-total-label"></p>
                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs">A almacén</span></label>
                        <input type="number" id="r-almacen" value="0" min="0"
                            class="input input-bordered input-sm" oninput="validarSumaRetorno()" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs">A destrucción</span></label>
                        <input type="number" id="r-destruccion" value="0" min="0"
                            class="input input-bordered input-sm" oninput="validarSumaRetorno()" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs">Perdidos</span></label>
                        <input type="number" id="r-perdidos" value="0" min="0"
                            class="input input-bordered input-sm" oninput="validarSumaRetorno()" />
                    </div>
                </div>
                <p class="text-sm font-medium" id="retorno-suma-info"></p>
            </div>

            <div class="form-control mb-3 hidden" id="campo-ubicacion">
                <label class="label"><span class="label-text text-xs">Ubicación destrucción</span></label>
                <select id="r-ubicacion" class="select select-bordered select-sm">
                    <option value="">Seleccionar caja...</option>
                    @for($i = 1; $i <= 50; $i++)
                        <option value="Caja {{ $i }}">Caja {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="form-control mb-3">
                <label class="label"><span class="label-text text-xs">Nombre candidato</span></label>
                <input type="text" id="r-candidato" class="input input-bordered input-sm" />
            </div>

            <div class="form-control mb-4 hidden" id="campo-razon">
                <label class="label"><span class="label-text text-xs">Razón de pérdida</span></label>
                <textarea id="r-razon" rows="2" class="textarea textarea-bordered textarea-sm"></textarea>
            </div>

            <div id="retorno-error" class="text-error text-sm hidden mb-2"></div>

            <div class="modal-action">
                <button onclick="confirmarRetorno()" class="btn btn-success gap-1">
                    <x-heroicon-o-check class="w-4 h-4" />
                    Confirmar retorno
                </button>
                <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

<script>
    const csrfToken         = '{{ csrf_token() }}';
    const solicitudId       = {{ $solicitud->ID_SOLICITUD }};
    const urlBuscarArt      = '{{ route("admin.solicitudes.buscar-articulo") }}';
    const urlAgregarArt     = '{{ url("admin/solicitudes") }}/' + solicitudId + '/examenes';
    const urlEliminarArt    = '{{ url("admin/solicitudes") }}/' + solicitudId + '/articulos';
    const urlEliminarExamen = '{{ url("admin/solicitudes") }}/' + solicitudId + '/examenes';
    const urlAgregarExamen  = '{{ url("admin/solicitudes/" . $solicitud->ID_SOLICITUD . "/examenes") }}';
    const urlSedes          = '{{ url("admin/solicitudes/empresa") }}';
    const urlContactos      = '{{ url("admin/solicitudes/sede") }}';

    let examenActualId    = null;
    let articuloRetornoId = null;
    let retornoTieneSerie = false;
    let retornoCantidad   = 0;
    let tipoBusquedaSol   = 'folio';

    function actualizarFechaMin() {
        const select = document.getElementById('tipo-examen-select');
        const opt    = select.options[select.selectedIndex];
        const dias   = parseInt(opt.dataset.dias ?? 0);
        const cands  = parseInt(opt.dataset.candidatos ?? 1);
        const label  = document.getElementById('dias-anticipacion-label');
        if (dias > 0) {
            const fecha = new Date();
            fecha.setDate(fecha.getDate() + dias);
            document.getElementById('examen-fecha').min = fecha.toISOString().split('T')[0];
            label.textContent = `mín. ${dias} días`;
        }
        document.getElementById('examen-cantidad').min   = cands;
        document.getElementById('examen-cantidad').value = Math.max(parseInt(document.getElementById('examen-cantidad').value), cands);
    }

    async function agregarExamen() {
        const tipoId   = document.getElementById('tipo-examen-select').value;
        const cantidad = document.getElementById('examen-cantidad').value;
        const fecha    = document.getElementById('examen-fecha').value;
        const errorEl  = document.getElementById('examen-error');
        errorEl.classList.add('hidden');
        if (!tipoId || !cantidad || !fecha) {
            errorEl.textContent = 'Completa todos los campos.';
            errorEl.classList.remove('hidden');
            return;
        }
        const res  = await fetch(urlAgregarExamen, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ tipo_examen_id: tipoId, cantidad, fecha }),
        });
        const data = await res.json();
        if (data.success) { location.reload(); }
        else { errorEl.textContent = data.error ?? 'Error al agregar.'; errorEl.classList.remove('hidden'); }
    }

    async function eliminarExamen(examenId) {
        if (!confirm('¿Eliminar este examen? Se devolverán sus artículos al almacén.')) return;
        const res  = await fetch(`${urlEliminarExamen}/${examenId}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        const data = await res.json();
        if (data.success) { document.getElementById(`examen-${examenId}`)?.remove(); }
        else { alert(data.error ?? 'Error al eliminar.'); }
    }

    function abrirModalArticulo(examenId) {
        examenActualId = examenId;
        document.getElementById('sol-resultado').innerHTML = '';
        document.getElementById('sol-input-busqueda').value = '';
        document.getElementById('modal-articulos').showModal();
    }

    async function buscarArticuloSol() {
        const busqueda = document.getElementById('sol-input-busqueda').value.trim();
        if (!busqueda) return;
        const tipo = document.querySelector('input[name="tipo_busqueda_sol"]:checked').value;
        const res  = await fetch(urlBuscarArt, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ busqueda, tipo_busqueda: tipo }),
        });
        const data = await res.json();
        const cont = document.getElementById('sol-resultado');
        window._solData = data;

        switch (data.tipo) {
            case 'serie_encontrada':
                if (data.articulo.CANTIDAD_ALMACEN < 1) {
                    cont.innerHTML = `<div class="alert alert-warning"><span>La serie <strong>${data.articulo.SERIE}</strong> no tiene existencia en almacén.</span></div>`;
                } else {
                    cont.innerHTML = `<div class="alert alert-info"><span>Serie <strong>${data.articulo.SERIE}</strong> — ${data.articulo.NOMBRE} / Almacén: ${data.articulo.CANTIDAD_ALMACEN}</span></div>
                    <button onclick="solAgregarSeries([${data.articulo.ID_ARTICULO}])" class="btn btn-primary btn-sm mt-2 gap-1"><x-heroicon-o-plus class="w-4 h-4" />Agregar</button>`;
                }
                break;
            case 'serie_nueva':
                cont.innerHTML = `<div class="alert alert-warning"><span>La serie <strong>${data.serie}</strong> no existe.</span></div>`;
                break;
            case 'folio_con_series':
                if (!data.disponibles.length) {
                    cont.innerHTML = `<div class="alert alert-warning"><span>No hay series disponibles en almacén para el folio <strong>${data.folio}</strong>.</span></div>`;
                } else {
                    const filas = data.disponibles.map(a => `
                        <tr>
                            <td><input type="checkbox" class="checkbox checkbox-sm sol-check-serie" value="${a.ID_ARTICULO}" checked /></td>
                            <td class="font-mono text-sm">${a.SERIE}</td>
                            <td class="text-center">${a.CANTIDAD_ALMACEN}</td>
                        </tr>`).join('');
                    cont.innerHTML = `<div class="alert alert-info mb-3"><span>${data.disponibles.length} series disponibles de ${data.total} totales.</span></div>
                    <div class="overflow-x-auto max-h-48 overflow-y-auto mb-3">
                        <table class="table table-sm">
                            <thead><tr>
                                <th><input type="checkbox" class="checkbox checkbox-sm" onchange="document.querySelectorAll('.sol-check-serie').forEach(c=>c.checked=this.checked)" checked /></th>
                                <th>Serie</th><th>Almacén</th>
                            </tr></thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                    <button onclick="solAgregarSeleccionadas()" class="btn btn-primary btn-sm gap-1"><x-heroicon-o-plus class="w-4 h-4" />Agregar seleccionadas</button>`;
                }
                break;
            case 'folio_granel':
                cont.innerHTML = `<div class="alert alert-info mb-3"><span>Folio <strong>${data.folio}</strong> — ${data.articulo.NOMBRE} / Almacén: <strong>${data.articulo.CANTIDAD_ALMACEN}</strong></span></div>
                <div class="flex items-end gap-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs">Cantidad</span></label>
                        <input type="number" id="sol-cantidad-granel" value="1" min="1" max="${data.articulo.CANTIDAD_ALMACEN}" class="input input-bordered input-sm w-28" />
                    </div>
                    <button onclick="solAgregarGranel(${data.articulo.ID_ARTICULO})" class="btn btn-primary btn-sm">Agregar</button>
                </div>`;
                break;
            case 'folio_nuevo':
                cont.innerHTML = `<div class="alert alert-warning"><span>El folio <strong>${data.folio}</strong> no existe.</span></div>`;
                break;
        }
    }

    async function solAgregarSeleccionadas() {
        const ids = [...document.querySelectorAll('.sol-check-serie:checked')].map(c => parseInt(c.value));
        if (!ids.length) { alert('Selecciona al menos una serie.'); return; }
        await solAgregarSeries(ids);
    }

    async function solAgregarSeries(ids) {
        const res  = await fetch(`${urlAgregarArt}/${examenActualId}/articulos`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ tipo: 'series', ids }),
        });
        const data = await res.json();
        if (data.success) { document.getElementById('modal-articulos').close(); location.reload(); }
        else { alert(data.error ?? 'Error.'); }
    }

    async function solAgregarGranel(idArticulo) {
        const cantidad = parseInt(document.getElementById('sol-cantidad-granel').value);
        const res  = await fetch(`${urlAgregarArt}/${examenActualId}/articulos`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ tipo: 'granel', id_articulo: idArticulo, cantidad }),
        });
        const data = await res.json();
        if (data.success) { document.getElementById('modal-articulos').close(); location.reload(); }
        else { alert(data.error ?? 'Error.'); }
    }

    async function eliminarArticulo(id) {
        if (!confirm('¿Quitar este artículo? Se devolverá al almacén.')) return;
        const res  = await fetch(`${urlEliminarArt}/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById(`art-${id}`)?.remove();
            document.getElementById(`art-mobile-${id}`)?.remove();
        } else { alert(data.error ?? 'Error.'); }
    }

    function abrirRetorno(id, tieneSerie, cantidad, folio, serie) {
        articuloRetornoId = id;
        retornoTieneSerie = tieneSerie;
        retornoCantidad   = cantidad;
        document.getElementById('retorno-descripcion').textContent =
            `Folio: ${folio}${serie ? ' / Serie: ' + serie : ''} — Cantidad enviada: ${cantidad}`;
        document.getElementById('retorno-con-serie').classList.toggle('hidden', !tieneSerie);
        document.getElementById('retorno-sin-serie').classList.toggle('hidden', tieneSerie);
        document.getElementById('retorno-error').classList.add('hidden');
        document.getElementById('campo-ubicacion').classList.add('hidden');
        document.getElementById('campo-razon').classList.add('hidden');
        document.getElementById('r-ubicacion').value = '';
        document.getElementById('r-razon').value     = '';
        document.getElementById('r-candidato').value = '';
        if (tieneSerie) {
            document.querySelector('input[name="destino"][value="almacen"]').checked = true;
        } else {
            document.getElementById('r-almacen').value     = cantidad;
            document.getElementById('r-destruccion').value = 0;
            document.getElementById('r-perdidos').value    = 0;
            document.getElementById('retorno-total-label').textContent = `Total a repartir: ${cantidad}`;
            validarSumaRetorno();
        }
        document.getElementById('modal-retorno').showModal();
    }

    function validarSumaRetorno() {
        const a    = parseInt(document.getElementById('r-almacen').value) || 0;
        const d    = parseInt(document.getElementById('r-destruccion').value) || 0;
        const p    = parseInt(document.getElementById('r-perdidos').value) || 0;
        const suma = a + d + p;
        const info = document.getElementById('retorno-suma-info');
        info.textContent = `Suma actual: ${suma} / ${retornoCantidad}`;
        info.className   = suma === retornoCantidad ? 'text-success text-sm' : 'text-error text-sm';
        document.getElementById('campo-ubicacion').classList.toggle('hidden', d === 0);
        document.getElementById('campo-razon').classList.toggle('hidden', p === 0);
    }

    async function confirmarRetorno() {
        const errorEl = document.getElementById('retorno-error');
        errorEl.classList.add('hidden');
        let payload = {
            nombre_candidato:      document.getElementById('r-candidato').value,
            ubicacion_destruccion: document.getElementById('r-ubicacion').value,
            razon_perdida:         document.getElementById('r-razon').value,
        };
        if (retornoTieneSerie) {
            payload.destino = document.querySelector('input[name="destino"]:checked').value;
        } else {
            payload.cantidad_almacen     = parseInt(document.getElementById('r-almacen').value) || 0;
            payload.cantidad_destruccion = parseInt(document.getElementById('r-destruccion').value) || 0;
            payload.cantidad_perdidos    = parseInt(document.getElementById('r-perdidos').value) || 0;
            if (payload.cantidad_almacen + payload.cantidad_destruccion + payload.cantidad_perdidos !== retornoCantidad) {
                errorEl.textContent = `La suma debe ser igual a ${retornoCantidad}.`;
                errorEl.classList.remove('hidden');
                return;
            }
        }
        const res  = await fetch(`${urlEliminarArt}/${articuloRetornoId}/retornar`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) { document.getElementById('modal-retorno').close(); location.reload(); }
        else { errorEl.textContent = data.error ?? 'Error al retornar.'; errorEl.classList.remove('hidden'); }
    }

    async function cargarSedesEdit(empresaId) {
        if (!empresaId) return;
        const res      = await fetch(`${urlSedes}/${empresaId}/sedes`);
        const sedes    = await res.json();
        const sel      = document.getElementById('edit-sede');
        sel.innerHTML  = '<option value="">Seleccionar...</option>';
        sedes.forEach(s => { sel.innerHTML += `<option value="${s.id}">${s.nombre}</option>`; });
    }

    async function cargarContactosEdit(sedeId) {
        if (!sedeId) return;
        const res       = await fetch(`${urlContactos}/${sedeId}/contactos`);
        const contactos = await res.json();
        const sel       = document.getElementById('edit-contacto');
        sel.innerHTML   = '<option value="">Seleccionar...</option>';
        contactos.forEach(c => { sel.innerHTML += `<option value="${c.id}">${c.nombre} ${c.apellidos}</option>`; });
    }

    async function onContactoChangeEdit(id) {
        if (!id) return;
        const sedeId = document.getElementById('edit-sede').value;
        if (!sedeId) return;
        const res       = await fetch(`${urlContactos}/${sedeId}/contactos`);
        const contactos = await res.json();
        const contacto  = contactos.find(c => c.id == id);
        if (!contacto) return;
        document.getElementById('edit-resp-nombre').value   = contacto.nombre + ' ' + contacto.apellidos;
        document.getElementById('edit-resp-correo').value   = contacto.correo   ?? '';
        document.getElementById('edit-resp-telefono').value = contacto.telefono ?? '';
        document.getElementById('edit-resp-celular').value  = '';
    }

    function onDestinoChange(valor) {
        document.getElementById('campo-ubicacion').classList.toggle('hidden', valor !== 'destruccion');
        document.getElementById('campo-razon').classList.toggle('hidden', valor !== 'perdido');
    }
</script>

</x-app-layout>