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
        <a href="{{ route('admin.solicitudes.show', $solicitud) }}" role="tab" class="tab">Datos</a>
        <a href="{{ route('admin.solicitudes.envio.show', $solicitud) }}" role="tab" class="tab tab-active">Envío</a>
        <a href="{{ route('admin.solicitudes.devolucion.show', $solicitud) }}" role="tab" class="tab">Devolución</a>
        <a href="{{ route('admin.solicitudes.facturacion.show', $solicitud) }}" role="tab" class="tab">Facturación</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- ═══════════ COLUMNA 1/3: DATOS DE CREACIÓN + ENVÍO ═══════════ --}}
        <div class="lg:col-span-1 flex flex-col gap-4">

            {{-- DATOS DE CREACIÓN (compacta, solo lectura) --}}
            <div class="card bg-base-200 shadow-sm">
                <div class="card-body p-3">
                    <h3 class="text-xs font-semibold uppercase text-base-content/50 mb-1">Datos de creación</h3>
                    <div class="text-xs space-y-0.5">
                        <p><span class="text-base-content/50">Empresa:</span> {{ $solicitud->empresa?->nombre ?? '—' }}</p>
                        <p><span class="text-base-content/50">Sede:</span> {{ $solicitud->sede?->nombre ?? '—' }}</p>
                        <p><span class="text-base-content/50">Contacto:</span> {{ $solicitud->contacto ? $solicitud->contacto->nombre . ' ' . $solicitud->contacto->apellidos : '—' }}</p>
                        <p><span class="text-base-content/50">Responsable:</span> {{ trim(($solicitud->RESPONSABLE_TITULO ? $solicitud->RESPONSABLE_TITULO . ' ' : '') . $solicitud->RESPONSABLE_NOMBRE) ?: '—' }}</p>
                        <p><span class="text-base-content/50">Fecha solicitud:</span> {{ $solicitud->FECHA_SOLICITUD?->format('d/m/Y') ?? '—' }}</p>
                        <p><span class="text-base-content/50">Fecha 1ª aplicación:</span> {{ $solicitud->FECHA_PRIMERA_APLICACION?->format('d/m/Y') ?? '—' }}</p>
                        <p>
                            <span class="text-base-content/50">Aprobación:</span>
                            @php
                                $badgeAprob = match($solicitud->APROBACION) {
                                    'aprobada'  => 'badge-success',
                                    'cancelada' => 'badge-error',
                                    default     => 'badge-warning',
                                };
                            @endphp
                            <span class="badge badge-xs {{ $badgeAprob }}">{{ ucfirst($solicitud->APROBACION ?? 'pendiente') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- DATOS DE ENVÍO --}}
            <div x-data="{ editando: false, cantidadUsb: {{ (int) $solicitud->CANTIDAD_USB }} }" class="card bg-base-100 shadow h-fit">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title text-base">Datos para carta de envío</h3>
                        <button type="button" class="btn btn-outline btn-info btn-xs" x-show="!editando" @click="editando = true">
                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                            Editar
                        </button>
                    </div>

                    {{-- SOLO LECTURA --}}
                    <div x-show="!editando" class="text-sm space-y-4">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-xs text-base-content/50 block">Examen</span>
                                <span class="font-medium">{{ $solicitud->ENVIO_EXAMEN ?: '—' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-base-content/50 block">Forma / Versión</span>
                                <span class="font-medium">{{ $solicitud->ENVIO_VERSION ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-xs text-base-content/50 block">Número de hojas</span>
                                <span class="font-medium">{{ $solicitud->ENVIO_NUMERO_HOJAS ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-base-content/50 block">Sobres blancos</span>
                                <span class="font-medium">{{ $solicitud->ENVIO_CANTIDAD_SOBRES ?? '—' }}</span>
                            </div>
                        </div>

                        @if($solicitud->CANTIDAD_USB > 0)
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-xs text-base-content/50 block">Pass USB</span>
                                    <span class="font-medium">{{ $solicitud->ENVIO_PASS_USB ?: '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-base-content/50 block">Folios de audio USB</span>
                                    <span class="font-medium">{{ $solicitud->ENVIO_FOLIOS_AUDIO ?: '—' }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="divider my-0"></div>

                        <div>
                            <h4 class="text-xs font-semibold uppercase text-base-content/50 mb-2">Datos de paquetería</h4>
                            <div class="mb-3">
                                <span class="text-xs text-base-content/50 block">Zona de envío</span>
                                <span class="font-medium">{{ $solicitud->ENVIO_ZONA === 'cdmx_area_metropolitana' ? 'CDMX / Área Metropolitana' : ($solicitud->ENVIO_ZONA === 'foraneo' ? 'Foráneo' : '—') }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <span class="text-xs text-base-content/50 block">Fecha de envío</span>
                                    <span class="font-medium">{{ $solicitud->ENVIO_FECHA_ENVIO?->format('d/m/Y') ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-base-content/50 block">Días permitidos</span>
                                    <span class="font-medium">{{ $solicitud->ENVIO_DIAS_PERMITIDO ?? '—' }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <span class="text-xs text-base-content/50 block">Paquetería</span>
                                    <span class="font-medium">{{ $solicitud->ENVIO_PAQUETERIA ?: '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-base-content/50 block">Número de guía</span>
                                    <span class="font-medium">{{ $solicitud->ENVIO_PAQUETERIA_GUIA ?: '—' }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="text-xs text-base-content/50 block">Costo de paquetería</span>
                                <span class="font-medium">{{ $solicitud->ENVIO_PAQUETERIA_COSTO ? '$' . number_format($solicitud->ENVIO_PAQUETERIA_COSTO, 2) : '—' }}</span>
                            </div>

                            <div>
                                <span class="text-xs text-base-content/50 block">Notas</span>
                                <span class="font-medium">{{ $solicitud->ENVIO_NOTAS ?: '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- FORMULARIO --}}
                    <form x-show="editando" method="POST" action="{{ route('admin.solicitudes.envio.update', $solicitud) }}">
                        @csrf @method('PATCH')

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Examen</span></label>
                                <input type="text" name="ENVIO_EXAMEN" value="{{ old('ENVIO_EXAMEN', $solicitud->ENVIO_EXAMEN) }}" class="input input-bordered input-sm w-full" />
                            </div>
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Forma / Versión</span></label>
                                <input type="text" name="ENVIO_VERSION" value="{{ old('ENVIO_VERSION', $solicitud->ENVIO_VERSION) }}" class="input input-bordered input-sm w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Número de hojas</span></label>
                                <input type="number" min="0" name="ENVIO_NUMERO_HOJAS" value="{{ old('ENVIO_NUMERO_HOJAS', $solicitud->ENVIO_NUMERO_HOJAS) }}" class="input input-bordered input-sm w-full" />
                            </div>
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Sobres blancos</span></label>
                                <input type="number" min="0" name="ENVIO_CANTIDAD_SOBRES" value="{{ old('ENVIO_CANTIDAD_SOBRES', $solicitud->ENVIO_CANTIDAD_SOBRES) }}" class="input input-bordered input-sm w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3" x-show="cantidadUsb > 0" x-cloak>
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Pass USB</span></label>
                                <input type="text" name="ENVIO_PASS_USB" value="{{ old('ENVIO_PASS_USB', $solicitud->ENVIO_PASS_USB) }}" class="input input-bordered input-sm w-full" />
                            </div>
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Folios de audio USB</span></label>
                                <input type="text" name="ENVIO_FOLIOS_AUDIO" value="{{ old('ENVIO_FOLIOS_AUDIO', $solicitud->ENVIO_FOLIOS_AUDIO) }}" class="input input-bordered input-sm w-full" />
                            </div>
                        </div>

                        <div class="divider my-1"></div>

                        <h4 class="text-xs font-semibold uppercase text-base-content/50 mb-2">Datos de paquetería</h4>

                        <div class="form-control mb-3">
                            <label class="label py-1"><span class="label-text text-xs">Zona de envío</span></label>
                            <select name="ENVIO_ZONA" class="select select-bordered select-sm w-full @error('ENVIO_ZONA') select-error @enderror">
                                <option value="">Selecciona…</option>
                                <option value="cdmx_area_metropolitana" {{ old('ENVIO_ZONA', $solicitud->ENVIO_ZONA) === 'cdmx_area_metropolitana' ? 'selected' : '' }}>CDMX / Área Metropolitana</option>
                                <option value="foraneo" {{ old('ENVIO_ZONA', $solicitud->ENVIO_ZONA) === 'foraneo' ? 'selected' : '' }}>Foráneo</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Fecha de envío</span></label>
                                <input type="date" name="ENVIO_FECHA_ENVIO" value="{{ old('ENVIO_FECHA_ENVIO', optional($solicitud->ENVIO_FECHA_ENVIO)->format('Y-m-d')) }}" class="input input-bordered input-sm w-full" />
                            </div>
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Días permitidos</span></label>
                                <input type="number" min="0" name="ENVIO_DIAS_PERMITIDO" value="{{ old('ENVIO_DIAS_PERMITIDO', $solicitud->ENVIO_DIAS_PERMITIDO) }}" class="input input-bordered input-sm w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Paquetería</span></label>
                                <input type="text" name="ENVIO_PAQUETERIA" value="{{ old('ENVIO_PAQUETERIA', $solicitud->ENVIO_PAQUETERIA) }}" class="input input-bordered input-sm w-full" />
                            </div>
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-xs">Número de guía</span></label>
                                <input type="text" name="ENVIO_PAQUETERIA_GUIA" value="{{ old('ENVIO_PAQUETERIA_GUIA', $solicitud->ENVIO_PAQUETERIA_GUIA) }}" class="input input-bordered input-sm w-full" />
                            </div>
                        </div>

                        <div class="form-control mb-3">
                            <label class="label py-1"><span class="label-text text-xs">Costo de paquetería</span></label>
                            <label class="input input-bordered input-sm w-full flex items-center gap-1">
                                <span class="text-base-content/50">$</span>
                                <input type="number" step="0.01" min="0" name="ENVIO_PAQUETERIA_COSTO"
                                    value="{{ old('ENVIO_PAQUETERIA_COSTO', $solicitud->ENVIO_PAQUETERIA_COSTO) }}"
                                    class="grow" />
                            </label>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label py-1"><span class="label-text text-xs">Notas</span></label>
                            <textarea name="ENVIO_NOTAS" rows="2" class="textarea textarea-bordered textarea-sm w-full">{{ old('ENVIO_NOTAS', $solicitud->ENVIO_NOTAS) }}</textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                            <button type="button" class="btn btn-ghost btn-sm" @click="editando = false">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════════ COLUMNA 2/3: EXÁMENES Y ARTÍCULOS (bloques) ═══════════ --}}
        <div class="card bg-base-100 shadow lg:col-span-2" x-data="examenesManager()">
            <div class="card-body">
                <h3 class="card-title text-base mb-4">Exámenes y artículos</h3>

                @forelse($examenesData as $data)
                    @php
                        [$examen, $bloques] = [$data['examen'], $data['bloques']];
                        $porFolio = $data['porFolio'];
                        $listo    = $data['listo'];
                    @endphp
                    <div class="collapse collapse-arrow bg-base-200 mb-2 rounded-lg">
                        <input type="checkbox" checked />
                        <div class="collapse-title font-medium py-3 min-h-0">
                            <div class="flex items-center justify-between pr-4 flex-wrap gap-1">
                                <span>
                                    {{ $examen->EXAMEN }} — {{ $examen->CANTIDAD }} candidatos — {{ $examen->FECHA?->format('d/m/Y') }}
                                    <span class="badge badge-sm badge-outline ml-1">
                                        Formato: {{ $examen->FORMATO ?: 'sin definir' }}
                                    </span>
                                </span>
                                <div class="flex items-center gap-1">
                                    <span class="badge badge-sm">{{ $examen->articulos->count() }} artículos</span>
                                    @if($listo)
                                        <span class="badge badge-sm badge-success">Listo</span>
                                    @else
                                        <span class="badge badge-sm badge-warning">Incompleto</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="collapse-content text-sm">
                            @if($solicitud->isPendiente())
                                <div class="flex gap-2 mb-3 flex-wrap">
                                    <button type="button" class="btn btn-outline btn-info btn-xs"
                                        @click="abrirEditarExamen({{ $examen->ID }}, @js($examen->EXAMEN), {{ $examen->CANTIDAD }}, @js(optional($examen->FECHA)->format('Y-m-d')), @js($examen->FORMATO))">
                                        <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                        Editar examen
                                    </button>
                                    <button type="button" class="btn btn-outline btn-primary btn-xs"
                                        @click="abrirModalArticulos({{ $examen->ID }})">
                                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                                        Agregar artículos
                                    </button>
                                    <form method="POST" action="{{ route('admin.solicitudes.examenes.destroy', [$solicitud, $examen]) }}"
                                        onsubmit="return confirm('¿Eliminar este examen y sus artículos asociados?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-error btn-xs">
                                            <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                            Eliminar examen
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if($bloques->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="table table-xs">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>ID Item</th>
                                                <th>Rango de folios</th>
                                                <th>Nombre</th>
                                                <th class="text-center">Cantidad</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bloques as $bIndex => $bloque)
                                                @php
                                                    $bloqueKey  = $examen->ID . '-' . $bIndex;
                                                    $diferencia = $porFolio[$bloque['folio']]['diferencia'] ?? 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        @if($bloque['count'] > 1)
                                                            <button type="button" class="btn btn-ghost btn-xs btn-square"
                                                                @click="toggleBloque('{{ $bloqueKey }}')">
                                                                <x-heroicon-o-chevron-right class="w-3.5 h-3.5 transition-transform"
                                                                    x-bind:class="bloqueAbierto === '{{ $bloqueKey }}' ? 'rotate-90' : ''" />
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td class="font-mono">{{ $bloque['folio'] }}</td>
                                                    <td class="font-mono text-xs">{{ $bloque['rango'] }} @if($bloque['count'] > 1)<span class="text-base-content/40">({{ $bloque['count'] }})</span>@endif</td>
                                                    <td>{{ $bloque['nombre'] }}</td>
                                                    <td class="text-center">
                                                        {{ $bloque['cantidad_enviada'] }}
                                                        @if($diferencia < 0)
                                                            <span class="badge badge-xs badge-error ml-1" title="Faltan artículos para este ID Item">-{{ abs($diferencia) }}</span>
                                                        @elseif($diferencia > 0)
                                                            <span class="badge badge-xs badge-warning ml-1" title="Sobran artículos para este ID Item">+{{ $diferencia }}</span>
                                                        @else
                                                            <span class="badge badge-xs badge-success ml-1" title="Completo">✓</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($solicitud->isPendiente())
                                                            <button type="button" class="btn btn-ghost btn-xs text-error"
                                                                title="Eliminar todo el bloque"
                                                                @click="eliminarBloque({{ json_encode($bloque['ids']) }})">
                                                                <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if($bloque['count'] > 1)
                                                    <tr x-show="bloqueAbierto === '{{ $bloqueKey }}'" x-cloak>
                                                        <td colspan="6" class="bg-base-100 p-0">
                                                            <table class="table table-xs w-full">
                                                                <tbody>
                                                                    @foreach($bloque['items'] as $item)
                                                                        <tr>
                                                                            <td class="w-8"></td>
                                                                            <td colspan="2" class="font-mono text-xs">{{ $item['serie'] }}</td>
                                                                            <td colspan="2"></td>
                                                                            <td>
                                                                                @if($solicitud->isPendiente())
                                                                                    <button type="button" class="btn btn-ghost btn-xs text-error"
                                                                                        title="Eliminar solo este folio"
                                                                                        @click="eliminarIndividual({{ $item['id'] }})">
                                                                                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                                                                    </button>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-xs text-base-content/40">Sin artículos agregados.</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50 text-center py-4">No hay exámenes agregados.</p>
                @endforelse

                @if($solicitud->isPendiente())
                    <button type="button" class="btn btn-ghost btn-sm gap-1 mt-2" @click="modalExamen = true">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Agregar examen
                    </button>
                @endif

                {{-- MODAL: AGREGAR EXAMEN --}}
                <div x-show="modalExamen" x-cloak class="modal" :class="{ 'modal-open': modalExamen }">
                    <div class="modal-box max-w-md">
                        <h3 class="font-bold text-lg mb-4">Agregar examen</h3>
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Tipo de examen</span></label>
                            <select x-model="nuevoExamen.tipo_examen_id" class="select select-bordered">
                                <option value="">Selecciona…</option>
                                @foreach($tipoExamenes as $te)
                                    <option value="{{ $te->id }}">{{ $te->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Cantidad de candidatos</span></label>
                            <input type="number" min="1" x-model="nuevoExamen.cantidad" class="input input-bordered" />
                        </div>
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Fecha</span></label>
                            <input type="date" x-model="nuevoExamen.fecha" class="input input-bordered" />
                        </div>
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Formato <span class="text-error">*</span></span></label>
                            <input type="text" x-model="nuevoExamen.formato" placeholder="Ej. A, B, Digital..." class="input input-bordered" />
                        </div>
                        <p class="text-error text-sm mb-2" x-show="errorExamen" x-text="errorExamen"></p>
                        <div class="modal-action">
                            <button type="button" class="btn btn-ghost btn-sm" @click="modalExamen = false">Cancelar</button>
                            <button type="button" class="btn btn-primary btn-sm" @click="guardarExamen()" :disabled="guardandoExamen">
                                <span x-show="!guardandoExamen">Guardar</span>
                                <span x-show="guardandoExamen">Guardando...</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- MODAL: EDITAR EXAMEN --}}
                <div x-show="modalEditarExamen" x-cloak class="modal" :class="{ 'modal-open': modalEditarExamen }">
                    <div class="modal-box max-w-md">
                        <h3 class="font-bold text-lg mb-4">Editar examen</h3>
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Nombre del examen</span></label>
                            <input type="text" x-model="examenEditando.examen" class="input input-bordered" />
                        </div>
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Cantidad de candidatos</span></label>
                            <input type="number" min="1" x-model="examenEditando.cantidad" class="input input-bordered" />
                        </div>
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Fecha</span></label>
                            <input type="date" x-model="examenEditando.fecha" class="input input-bordered" />
                        </div>
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Formato <span class="text-error">*</span></span></label>
                            <input type="text" x-model="examenEditando.formato" placeholder="Ej. A, B, Digital..." class="input input-bordered" />
                        </div>
                        <p class="text-error text-sm mb-2" x-show="errorEditarExamen" x-text="errorEditarExamen"></p>
                        <div class="modal-action">
                            <button type="button" class="btn btn-ghost btn-sm" @click="modalEditarExamen = false">Cancelar</button>
                            <button type="button" class="btn btn-primary btn-sm" @click="guardarEdicionExamen()" :disabled="guardandoEditarExamen">
                                <span x-show="!guardandoEditarExamen">Guardar</span>
                                <span x-show="guardandoEditarExamen">Guardando...</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- MODAL: AGREGAR ARTÍCULOS --}}
                <div x-show="modalArticulo" x-cloak class="modal" :class="{ 'modal-open': modalArticulo }">
                    <div class="modal-box max-w-lg">
                        <h3 class="font-bold text-lg mb-1">Agregar artículos</h3>

                        {{-- FASE 1: CAPTURA --}}
                        <template x-if="faseArticulo === 'captura'">
                            <div>
                                <p class="text-sm text-base-content/60 mb-4">
                                    Ingresa uno o varios folios/ID Items (uno por línea). Acepta folio individual, rango (S000000001-S000000010) o ID Item con cantidad (ID:5).
                                    Los artículos deben coincidir con el formato del examen.
                                </p>
                                <div class="form-control mb-4">
                                    <textarea x-model="entradasTexto" rows="6" placeholder="S000000001&#10;S000000010-S000000020&#10;PZ-AUDIO:10"
                                        class="textarea textarea-bordered font-mono text-sm"></textarea>
                                </div>
                                <p class="text-error text-sm mb-2" x-show="errorArticulo" x-text="errorArticulo"></p>
                                <div class="modal-action">
                                    <button type="button" class="btn btn-ghost btn-sm" @click="modalArticulo = false">Cerrar</button>
                                    <button type="button" class="btn btn-primary btn-sm" @click="revisarArticulos()" :disabled="revisandoArticulo">
                                        <span x-show="!revisandoArticulo">Revisar</span>
                                        <span x-show="revisandoArticulo">Revisando...</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- FASE 2: RESULTADO DE LA REVISIÓN --}}
                        <template x-if="faseArticulo === 'revisado'">
                            <div>
                                <p class="text-sm text-base-content/60 mb-3">
                                    Revisa el resultado antes de confirmar. Solo se agregarán los artículos marcados como válidos.
                                </p>

                                <div x-show="revision.agregados && revision.agregados.length" class="alert alert-success text-xs mb-2 items-start">
                                    <div>
                                        <p class="font-semibold mb-1" x-text="'Se pueden agregar (' + revision.agregados.length + '):'"></p>
                                        <div class="max-h-32 overflow-y-auto space-y-0.5">
                                            <template x-for="item in revision.agregados" :key="item">
                                                <p x-text="item"></p>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="revision.no_agregados && revision.no_agregados.length" class="alert alert-warning text-xs mb-2 items-start">
                                    <div>
                                        <p class="font-semibold mb-1" x-text="'No se podrán agregar (' + revision.no_agregados.length + '):'"></p>
                                        <div class="max-h-32 overflow-y-auto space-y-0.5">
                                            <template x-for="msg in revision.no_agregados" :key="msg">
                                                <p x-text="msg"></p>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-error text-sm mb-2" x-show="errorArticulo" x-text="errorArticulo"></p>

                                <div class="modal-action">
                                    <button type="button" class="btn btn-ghost btn-sm" @click="volverACaptura()">← Editar lista</button>
                                    <button type="button" class="btn btn-primary btn-sm"
                                        @click="confirmarArticulos()"
                                        :disabled="guardandoArticulo || !(revision.agregados && revision.agregados.length)">
                                        <span x-show="!guardandoArticulo">Confirmar y agregar</span>
                                        <span x-show="guardandoArticulo">Agregando...</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- FASE 3: ADVERTENCIAS DE CANTIDAD (informativo, no bloquea) --}}
                        <template x-if="faseArticulo === 'advertencias'">
                            <div>
                                <div class="alert alert-success text-sm mb-3">
                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                    <span>Artículos agregados correctamente.</span>
                                </div>

                                <div x-show="advertenciasCantidad.length" class="alert alert-warning text-xs mb-2 items-start">
                                    <div>
                                        <p class="font-semibold mb-1">Aviso de cantidades:</p>
                                        <div class="max-h-32 overflow-y-auto space-y-0.5">
                                            <template x-for="msg in advertenciasCantidad" :key="msg">
                                                <p x-text="msg"></p>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-action">
                                    <button type="button" class="btn btn-primary btn-sm" @click="window.location.reload()">Cerrar</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- ═══════════ ACCIONES FINALES ═══════════ --}}
    <div class="card bg-base-100 shadow mt-4">
        <div class="card-body flex-row items-center justify-end gap-2 flex-wrap">
            <a href="{{ route('admin.solicitudes.carta', $solicitud) }}" class="btn btn-outline btn-sm gap-1">
                <x-heroicon-o-document-arrow-down class="w-4 h-4" />
                Carta de envío (Word)
            </a>

            @if($solicitud->isPendiente())
                <div class="tooltip" data-tip="@if(!$puedeMarcarEnviada) Cada examen debe tener al menos un ID Item, y la cantidad de artículos por ID Item debe cubrir el número de candidatos. @endif">
                    <form method="POST" action="{{ route('admin.solicitudes.estado', $solicitud) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="enviada">
                        <button type="submit" class="btn btn-primary btn-sm gap-1"
                            @disabled(!$puedeMarcarEnviada)
                            onclick="return confirm('¿Marcar esta solicitud como enviada?')">
                            <x-heroicon-o-paper-airplane class="w-4 h-4" />
                            Marcar como enviada
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        function examenesManager() {
            return {
                modalExamen: false,
                modalEditarExamen: false,
                modalArticulo: false,
                guardandoExamen: false,
                guardandoEditarExamen: false,
                revisandoArticulo: false,
                guardandoArticulo: false,
                errorExamen: '',
                errorEditarExamen: '',
                errorArticulo: '',
                examenActual: null,
                nuevoExamen: { tipo_examen_id: '', cantidad: '', fecha: '', formato: '' },
                examenEditando: { id: null, examen: '', cantidad: '', fecha: '', formato: '' },
                entradasTexto: '',
                faseArticulo: 'captura',
                revision: { agregados: [], no_agregados: [] },
                advertenciasCantidad: [],
                resultado: {},
                bloqueAbierto: null,

                abrirModalArticulos(examenId) {
                    this.examenActual = examenId;
                    this.faseArticulo = 'captura';
                    this.entradasTexto = '';
                    this.revision = { agregados: [], no_agregados: [] };
                    this.errorArticulo = '';
                    this.modalArticulo = true;
                },

                abrirEditarExamen(id, examen, cantidad, fecha, formato) {
                    this.errorEditarExamen = '';
                    this.examenEditando = { id, examen, cantidad, fecha, formato: formato || '' };
                    this.modalEditarExamen = true;
                },

                toggleBloque(key) {
                    this.bloqueAbierto = this.bloqueAbierto === key ? null : key;
                },

                async guardarExamen() {
                    this.errorExamen = '';
                    this.guardandoExamen = true;
                    try {
                        const res = await fetch("{{ route('admin.solicitudes.examenes.store', $solicitud) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.nuevoExamen),
                        });
                        const data = await res.json();
                        if (!res.ok || data.error) {
                            this.errorExamen = data.error || 'Error al guardar.';
                            return;
                        }
                        window.location.reload();
                    } finally {
                        this.guardandoExamen = false;
                    }
                },

                async guardarEdicionExamen() {
                    this.errorEditarExamen = '';
                    this.guardandoEditarExamen = true;
                    try {
                        const res = await fetch(`/admin/solicitudes/{{ $solicitud->ID_SOLICITUD }}/examenes/${this.examenEditando.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                examen: this.examenEditando.examen,
                                cantidad: this.examenEditando.cantidad,
                                fecha: this.examenEditando.fecha,
                                formato: this.examenEditando.formato,
                            }),
                        });
                        const data = await res.json();
                        if (!res.ok || data.error) {
                            this.errorEditarExamen = data.error || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Error al guardar.');
                            return;
                        }
                        window.location.reload();
                    } catch (e) {
                        this.errorEditarExamen = 'Error al guardar los cambios.';
                    } finally {
                        this.guardandoEditarExamen = false;
                    }
                },

                async revisarArticulos() {
                    this.errorArticulo = '';
                    const entradas = this.entradasTexto.split('\n').map(e => e.trim()).filter(Boolean);
                    if (!entradas.length) {
                        this.errorArticulo = 'Ingresa al menos un folio o ID Item.';
                        return;
                    }
                    this.revisandoArticulo = true;
                    try {
                        const res = await fetch(`/admin/solicitudes/{{ $solicitud->ID_SOLICITUD }}/examenes/${this.examenActual}/articulos/revisar`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ entradas }),
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            this.errorArticulo = data.message || 'Error al revisar.';
                            return;
                        }
                        this.revision = data;
                        this.faseArticulo = 'revisado';
                    } finally {
                        this.revisandoArticulo = false;
                    }
                },

                volverACaptura() {
                    this.faseArticulo = 'captura';
                },

                async confirmarArticulos() {
                    this.errorArticulo = '';
                    this.guardandoArticulo = true;
                    const entradas = this.entradasTexto.split('\n').map(e => e.trim()).filter(Boolean);
                    try {
                        const res = await fetch(`/admin/solicitudes/{{ $solicitud->ID_SOLICITUD }}/examenes/${this.examenActual}/articulos`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ entradas }),
                        });
                        const data = await res.json();
                        this.resultado = data;
                        if (data.success) {
                            this.advertenciasCantidad = data.advertencias || [];
                            if (this.advertenciasCantidad.length) {
                                this.faseArticulo = 'advertencias';
                                this.guardandoArticulo = false;
                            } else {
                                setTimeout(() => window.location.reload(), 800);
                            }
                            return;
                        }
                        this.errorArticulo = data.message || 'No se pudo agregar ningún artículo.';
                        this.guardandoArticulo = false;
                    } catch (e) {
                        this.errorArticulo = 'Error al confirmar los artículos.';
                        this.guardandoArticulo = false;
                    }
                },

                async eliminarBloque(ids) {
                    if (!confirm(`¿Eliminar ${ids.length} artículo(s) de este bloque? Esta acción no se puede deshacer.`)) return;
                    try {
                        const res = await fetch(`/admin/solicitudes/{{ $solicitud->ID_SOLICITUD }}/articulos-lote`, {
                            method: 'DELETE',
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
                    } catch (e) {
                        alert('Error al eliminar el bloque.');
                    }
                },

                async eliminarIndividual(id) {
                    if (!confirm('¿Eliminar este folio de la solicitud?')) return;
                    try {
                        const res = await fetch(`/admin/solicitudes/{{ $solicitud->ID_SOLICITUD }}/articulos/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json().catch(() => ({}));
                        if (res.ok) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error al eliminar el folio.');
                        }
                    } catch (e) {
                        alert('Error al eliminar el folio.');
                    }
                },
            }
        }
    </script>
</x-app-layout>