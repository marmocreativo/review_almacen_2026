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
            @if($solicitud->isPendiente())
                <form method="POST" action="{{ route('admin.solicitudes.estado', $solicitud) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="estado" value="enviada">
                    <button type="submit" class="btn btn-primary btn-sm gap-1"
                        onclick="return confirm('¿Marcar esta solicitud como enviada?')">
                        <x-heroicon-o-paper-airplane class="w-4 h-4" />
                        Marcar como enviada
                    </button>
                </form>
            @endif
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

        {{-- ═══════════ COLUMNA 1/3: DATOS DE ENVÍO ═══════════ --}}
        <div x-data="{ editando: false }" class="card bg-base-100 shadow lg:col-span-1 h-fit">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title text-base">Datos de envío</h3>
                    <button type="button" class="btn btn-outline btn-info btn-xs" x-show="!editando" @click="editando = true">
                        <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                        Editar
                    </button>
                </div>

                {{-- SOLO LECTURA: tabla --}}
                <div x-show="!editando" class="overflow-x-auto">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td class="text-xs text-base-content/50 w-1/2">Zona de envío</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_ZONA === 'cdmx_area_metropolitana' ? 'CDMX / Área Metropolitana' : ($solicitud->ENVIO_ZONA === 'foraneo' ? 'Foráneo' : '—') }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Examen</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_EXAMEN ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Forma / Versión</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_VERSION ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Número de hojas</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_NUMERO_HOJAS ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Sobres blancos</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_CANTIDAD_SOBRES ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Pass USB</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_PASS_USB ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Folios de audio USB</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_FOLIOS_AUDIO ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Fecha de envío</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_FECHA_ENVIO?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Días permitidos</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_DIAS_PERMITIDO ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Paquetería</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_PAQUETERIA ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Número de guía</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_PAQUETERIA_GUIA ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50">Costo de paquetería</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_PAQUETERIA_COSTO ? '$' . number_format($solicitud->ENVIO_PAQUETERIA_COSTO, 2) : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-base-content/50 align-top">Notas</td>
                                <td class="font-medium text-right">{{ $solicitud->ENVIO_NOTAS ?: '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- FORMULARIO: campos a ancho completo --}}
                <form x-show="editando" method="POST" action="{{ route('admin.solicitudes.envio.update', $solicitud) }}">
                    @csrf @method('PATCH')

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Zona de envío</span></label>
                        <select name="ENVIO_ZONA" class="select select-bordered select-sm w-full @error('ENVIO_ZONA') select-error @enderror">
                            <option value="">Selecciona…</option>
                            <option value="cdmx_area_metropolitana" {{ old('ENVIO_ZONA', $solicitud->ENVIO_ZONA) === 'cdmx_area_metropolitana' ? 'selected' : '' }}>CDMX / Área Metropolitana</option>
                            <option value="foraneo" {{ old('ENVIO_ZONA', $solicitud->ENVIO_ZONA) === 'foraneo' ? 'selected' : '' }}>Foráneo</option>
                        </select>
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Examen</span></label>
                        <input type="text" name="ENVIO_EXAMEN" value="{{ old('ENVIO_EXAMEN', $solicitud->ENVIO_EXAMEN) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Forma / Versión</span></label>
                        <input type="text" name="ENVIO_VERSION" value="{{ old('ENVIO_VERSION', $solicitud->ENVIO_VERSION) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Número de hojas</span></label>
                        <input type="number" min="0" name="ENVIO_NUMERO_HOJAS" value="{{ old('ENVIO_NUMERO_HOJAS', $solicitud->ENVIO_NUMERO_HOJAS) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Sobres blancos</span></label>
                        <input type="number" min="0" name="ENVIO_CANTIDAD_SOBRES" value="{{ old('ENVIO_CANTIDAD_SOBRES', $solicitud->ENVIO_CANTIDAD_SOBRES) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Pass USB</span></label>
                        <input type="text" name="ENVIO_PASS_USB" value="{{ old('ENVIO_PASS_USB', $solicitud->ENVIO_PASS_USB) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Folios de audio USB</span></label>
                        <input type="text" name="ENVIO_FOLIOS_AUDIO" value="{{ old('ENVIO_FOLIOS_AUDIO', $solicitud->ENVIO_FOLIOS_AUDIO) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Fecha de envío</span></label>
                        <input type="date" name="ENVIO_FECHA_ENVIO" value="{{ old('ENVIO_FECHA_ENVIO', optional($solicitud->ENVIO_FECHA_ENVIO)->format('Y-m-d')) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Días permitidos</span></label>
                        <input type="number" min="0" name="ENVIO_DIAS_PERMITIDO" value="{{ old('ENVIO_DIAS_PERMITIDO', $solicitud->ENVIO_DIAS_PERMITIDO) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Paquetería</span></label>
                        <input type="text" name="ENVIO_PAQUETERIA" value="{{ old('ENVIO_PAQUETERIA', $solicitud->ENVIO_PAQUETERIA) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Número de guía</span></label>
                        <input type="text" name="ENVIO_PAQUETERIA_GUIA" value="{{ old('ENVIO_PAQUETERIA_GUIA', $solicitud->ENVIO_PAQUETERIA_GUIA) }}" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control mb-3">
                        <label class="label py-1"><span class="label-text text-xs">Costo de paquetería</span></label>
                        <input type="number" step="0.01" min="0" name="ENVIO_PAQUETERIA_COSTO" value="{{ old('ENVIO_PAQUETERIA_COSTO', $solicitud->ENVIO_PAQUETERIA_COSTO) }}" class="input input-bordered input-sm w-full" />
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

        {{-- ═══════════ COLUMNA 2/3: EXÁMENES Y ARTÍCULOS (bloques) ═══════════ --}}
        <div class="card bg-base-100 shadow lg:col-span-2" x-data="examenesManager()">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title text-base">Exámenes y artículos</h3>
                    @if($solicitud->isPendiente())
                        <button type="button" class="btn btn-primary btn-sm gap-1" @click="modalExamen = true">
                            <x-heroicon-o-plus class="w-4 h-4" />
                            Agregar examen
                        </button>
                    @endif
                </div>

                @forelse($examenesData as $data)
                    @php [$examen, $bloques] = [$data['examen'], $data['bloques']]; @endphp
                    <div class="collapse collapse-arrow bg-base-200 mb-2 rounded-lg">
                        <input type="checkbox" />
                        <div class="collapse-title font-medium py-3 min-h-0">
                            <div class="flex items-center justify-between pr-4">
                                <span>{{ $examen->EXAMEN }} — {{ $examen->CANTIDAD }} candidatos — {{ $examen->FECHA?->format('d/m/Y') }}</span>
                                <span class="badge badge-sm">{{ $examen->articulos->count() }} artículos</span>
                            </div>
                        </div>
                        <div class="collapse-content text-sm">
                            @if($solicitud->isPendiente())
                                <div class="flex gap-2 mb-3">
                                    <button type="button" class="btn btn-outline btn-primary btn-xs"
                                        @click="modalArticulo = true; examenActual = {{ $examen->ID }}">
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
                                                <th>ID Item</th>
                                                <th>Rango de folios</th>
                                                <th>Nombre</th>
                                                <th class="text-center">Cantidad</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bloques as $bloque)
                                                <tr>
                                                    <td class="font-mono">{{ $bloque['folio'] }}</td>
                                                    <td class="font-mono text-xs">{{ $bloque['rango'] }} @if($bloque['count'] > 1)<span class="text-base-content/40">({{ $bloque['count'] }})</span>@endif</td>
                                                    <td>{{ $bloque['nombre'] }}</td>
                                                    <td class="text-center">{{ $bloque['cantidad_enviada'] }}</td>
                                                    <td>
                                                        @if($solicitud->isPendiente())
                                                            <button type="button" class="btn btn-ghost btn-xs text-error"
                                                                @click="eliminarBloque({{ json_encode($bloque['ids']) }})">
                                                                <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
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
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Fecha</span></label>
                            <input type="date" x-model="nuevoExamen.fecha" class="input input-bordered" />
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

                {{-- MODAL: AGREGAR ARTÍCULOS --}}
                <div x-show="modalArticulo" x-cloak class="modal" :class="{ 'modal-open': modalArticulo }">
                    <div class="modal-box max-w-lg">
                        <h3 class="font-bold text-lg mb-1">Agregar artículos</h3>
                        <p class="text-sm text-base-content/60 mb-4">
                            Ingresa uno o varios folios/ID Items (uno por línea). Acepta folio individual, rango (S000000001-S000000010) o ID Item con cantidad (ID:5).
                        </p>
                        <div class="form-control mb-4">
                            <textarea x-model="entradasTexto" rows="6" placeholder="S000000001&#10;S000000010-S000000020&#10;PZ-AUDIO:10"
                                class="textarea textarea-bordered font-mono text-sm"></textarea>
                        </div>
                        <div x-show="resultado.no_agregados && resultado.no_agregados.length" class="alert alert-warning text-xs mb-2">
                            <div>
                                <p class="font-semibold mb-1">No se agregaron:</p>
                                <template x-for="msg in resultado.no_agregados" :key="msg">
                                    <p x-text="msg"></p>
                                </template>
                            </div>
                        </div>
                        <p class="text-error text-sm mb-2" x-show="errorArticulo" x-text="errorArticulo"></p>
                        <div class="modal-action">
                            <button type="button" class="btn btn-ghost btn-sm" @click="modalArticulo = false">Cerrar</button>
                            <button type="button" class="btn btn-primary btn-sm" @click="guardarArticulos()" :disabled="guardandoArticulo">
                                <span x-show="!guardandoArticulo">Agregar</span>
                                <span x-show="guardandoArticulo">Agregando...</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        function examenesManager() {
            return {
                modalExamen: false,
                modalArticulo: false,
                guardandoExamen: false,
                guardandoArticulo: false,
                errorExamen: '',
                errorArticulo: '',
                examenActual: null,
                nuevoExamen: { tipo_examen_id: '', cantidad: '', fecha: '' },
                entradasTexto: '',
                resultado: {},

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

                async guardarArticulos() {
                    this.errorArticulo = '';
                    this.guardandoArticulo = true;
                    const entradas = this.entradasTexto.split('\n').map(e => e.trim()).filter(Boolean);
                    if (!entradas.length) {
                        this.errorArticulo = 'Ingresa al menos un folio o ID Item.';
                        this.guardandoArticulo = false;
                        return;
                    }
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
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            this.errorArticulo = data.message || 'No se pudo agregar ningún artículo.';
                        }
                    } finally {
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
                }
            }
        }
    </script>
</x-app-layout>