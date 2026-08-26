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
        <a href="{{ route('admin.solicitudes.envio.show', $solicitud) }}" role="tab" class="tab">Envío</a>
        <a href="{{ route('admin.solicitudes.devolucion.show', $solicitud) }}" role="tab" class="tab tab-active">Devolución</a>
        <a href="{{ route('admin.solicitudes.facturacion.show', $solicitud) }}" role="tab" class="tab">Facturación</a>
    </div>

    @if(!$solicitud->isEnviada() && !$solicitud->isRetornada())
        <div class="alert alert-warning mb-4">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
            <span>Esta solicitud aún está en estado "pendiente". Debe marcarse como enviada antes de procesar devoluciones.</span>
        </div>
    @endif

    @if($solicitud->isRetornada())
        <div class="flex justify-end mb-4" x-data="{ reeditando: false }" x-init="window.dispatchEvent(new CustomEvent('reeditando-init'))" @reeditando-toggle.window="reeditando = !reeditando">
            <button type="button" class="btn btn-outline btn-warning btn-sm gap-1"
                @click="window.dispatchEvent(new CustomEvent('reeditando-toggle'))">
                <x-heroicon-o-pencil class="w-4 h-4" />
                <span x-text="reeditando ? 'Cancelar reedición' : 'Reeditar devolución'"></span>
            </button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.solicitudes.devolucion.procesar', $solicitud) }}" id="form-devolucion"
        x-data="{ reeditando: false }" @reeditando-toggle.window="reeditando = !reeditando"
        @change="$store.devolucion.recalc()">
        @csrf
        <input type="hidden" name="reedicion" x-bind:value="reeditando ? 1 : 0" />
        @if($cajasAbiertas->isNotEmpty())
            <div class="card bg-base-100 shadow mb-4">
                <div class="card-body py-4">
                    <div class="form-control max-w-sm">
                        <label class="label py-1">
                            <span class="label-text font-medium">Caja destino para artículos aplicados/dañados</span>
                        </label>
                        <select name="id_caja_destino" class="select select-bordered select-sm">
                            <option value="">Automático (primera caja disponible o nueva)</option>
                            @foreach($cajasAbiertas as $cajaOpcion)
                                <option value="{{ $cajaOpcion->ID }}">{{ $cajaOpcion->NOMBRE }}</option>
                            @endforeach
                        </select>
                        <label class="label py-1">
                            <span class="label-text-alt text-base-content/40">
                                Si no seleccionas ninguna, el sistema usará la caja abierta de número más bajo, o creará una nueva si no hay ninguna disponible.
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        @endif

        @forelse($solicitud->examenes as $examen)
            @php
                $pendientes = $examen->articulos->where('ESTADO', '!=', 'retornado');
                $retornados = $examen->articulos->where('ESTADO', 'retornado');
            @endphp
            <div class="card bg-base-100 shadow mb-4">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                        <h3 class="card-title text-base">{{ $examen->EXAMEN }} — {{ $examen->FECHA?->format('d/m/Y') }}</h3>
                        <span class="badge badge-sm {{ $pendientes->count() > 0 ? 'badge-warning' : 'badge-success' }}">
                            {{ $pendientes->count() > 0 ? $pendientes->count() . ' pendientes' : 'Completo' }}
                        </span>
                    </div>

                    @if($pendientes->count() > 0 && $solicitud->isEnviada())
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="text-xs text-base-content/50 self-center">Marcar todos como:</span>
                            <button type="button" class="btn btn-outline btn-success btn-xs" onclick="marcarTodos('examen-{{ $examen->ID }}', 'aplicado')">Aplicado</button>
                            <button type="button" class="btn btn-outline btn-warning btn-xs" onclick="marcarTodos('examen-{{ $examen->ID }}', 'no_aplicado')">No aplicado</button>
                            <button type="button" class="btn btn-outline btn-error btn-xs" onclick="marcarTodos('examen-{{ $examen->ID }}', 'danado')">Dañado</button>
                            <button type="button" class="btn btn-outline btn-error btn-xs" onclick="marcarTodos('examen-{{ $examen->ID }}', 'faltante')">Faltante</button>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID Item</th>
                                    <th>Folio</th>
                                    <th>Nombre</th>
                                    <th>Candidato</th>
                                    <th>Estado de devolución</th>
                                </tr>
                            </thead>
                            <tbody data-examen-group="examen-{{ $examen->ID }}">
                                @foreach($examen->articulos as $sa)
                                    <tr>
                                        <td class="font-mono">{{ $sa->FOLIO }}</td>
                                        <td class="font-mono text-xs">{{ $sa->SERIE ?: '—' }}</td>
                                        <td>{{ $sa->NOMBRE }}</td>
                                        @if($sa->isRetornado())
                                            <td x-show="!reeditando" class="text-xs text-base-content/60">{{ $sa->NOMBRE_CANDIDATO ?: '—' }}</td>
                                            <td x-show="!reeditando">
                                                @php
                                                    $badgeDev = match($sa->ESTADO_DEVOLUCION) {
                                                        'aplicado'    => 'badge-success',
                                                        'no_aplicado' => 'badge-warning',
                                                        'danado'      => 'badge-error',
                                                        'faltante'    => 'badge-error',
                                                        default       => 'badge-ghost',
                                                    };
                                                @endphp
                                                <span class="badge badge-sm {{ $badgeDev }}">
                                                    {{ $sa->ESTADO_DEVOLUCION ? ucfirst(str_replace('_', ' ', $sa->ESTADO_DEVOLUCION)) : 'Retornado' }}
                                                </span>
                                            </td>

                                            <td x-show="reeditando" x-cloak>
                                                <input type="hidden" name="items[{{ $sa->ID }}][id]" value="{{ $sa->ID }}" />
                                                <input type="text" name="items[{{ $sa->ID }}][nombre_candidato]"
                                                    value="{{ $sa->NOMBRE_CANDIDATO }}"
                                                    placeholder="Nombre del candidato"
                                                    class="input input-bordered input-xs w-full" />
                                            </td>
                                            <td x-show="reeditando" x-cloak>
                                                <select name="items[{{ $sa->ID }}][estado_devolucion]"
                                                    class="select select-bordered select-xs w-full item-estado-devolucion">
                                                    <option value="">Selecciona…</option>
                                                    <option value="aplicado" {{ $sa->ESTADO_DEVOLUCION === 'aplicado' ? 'selected' : '' }}>Aplicado</option>
                                                    <option value="no_aplicado" {{ $sa->ESTADO_DEVOLUCION === 'no_aplicado' ? 'selected' : '' }}>No aplicado</option>
                                                    <option value="danado" {{ $sa->ESTADO_DEVOLUCION === 'danado' ? 'selected' : '' }}>Dañado</option>
                                                    <option value="faltante" {{ $sa->ESTADO_DEVOLUCION === 'faltante' ? 'selected' : '' }}>Faltante</option>
                                                </select>
                                            </td>
                                        @else
                                            <td>
                                                <input type="hidden" name="items[{{ $sa->ID }}][id]" value="{{ $sa->ID }}" />
                                                <input type="text" name="items[{{ $sa->ID }}][nombre_candidato]"
                                                    value="{{ $sa->NOMBRE_CANDIDATO }}"
                                                    placeholder="Nombre del candidato"
                                                    class="input input-bordered input-xs w-full" />
                                            </td>
                                            <td>
                                                <select name="items[{{ $sa->ID }}][estado_devolucion]"
                                                    class="select select-bordered select-xs w-full item-estado-devolucion">
                                                    <option value="">Selecciona…</option>
                                                    <option value="aplicado">Aplicado</option>
                                                    <option value="no_aplicado">No aplicado</option>
                                                    <option value="danado">Dañado</option>
                                                    <option value="faltante">Faltante</option>
                                                </select>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <p class="text-sm text-base-content/50 text-center py-4">No hay artículos registrados en esta solicitud.</p>
                </div>
            </div>
        @endforelse

        @if($solicitud->isEnviada())
            <div class="flex items-center justify-end gap-3 flex-wrap">
                <span class="text-sm text-base-content/60">
                    Artículos <span class="font-semibold" x-text="$store.devolucion.procesados"></span> / <span x-text="$store.devolucion.total"></span>
                </span>
                <button type="submit"
                    class="btn gap-1"
                    :class="$store.devolucion.procesados < $store.devolucion.total ? 'btn-warning' : 'btn-primary'"
                    onclick="return confirm('¿Procesar la devolución de los artículos marcados? Esta acción no se puede deshacer.')">
                    <x-heroicon-o-check class="w-4 h-4" />
                    <span x-text="$store.devolucion.procesados < $store.devolucion.total ? 'Procesar retorno parcial' : 'Procesar todos los artículos'"></span>
                </button>
            </div>
        @elseif($solicitud->isRetornada())
            <div class="flex justify-end" x-show="reeditando" x-cloak>
                <button type="submit" class="btn btn-warning gap-1"
                    onclick="return confirm('¿Guardar la reedición de esta devolución? Quedará registrado en bitácora.')">
                    <x-heroicon-o-check class="w-4 h-4" />
                    Guardar reedición
                </button>
            </div>
        @endif
    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('devolucion', {
                procesados: {{ $solicitud->articulos->where('ESTADO', 'retornado')->count() }},
                total: {{ $solicitud->articulos->count() }},
                recalc() {
                    this.procesados = Array.from(
                        document.querySelectorAll('select[name^="items"][name$="[estado_devolucion]"]')
                    ).filter(select => select.value !== '').length;
                },
            });
        });

        function marcarTodos(grupo, valor) {
            const tbody = document.querySelector(`tbody[data-examen-group="${grupo}"]`);
            if (!tbody) return;
            tbody.querySelectorAll('.item-estado-devolucion').forEach(select => {
                select.value = valor;
            });
            if (window.Alpine && Alpine.store('devolucion')) {
                Alpine.store('devolucion').recalc();
            }
        }
    </script>
</x-app-layout>