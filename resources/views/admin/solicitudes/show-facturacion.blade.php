<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-ghost btn-sm btn-square">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
            </a>
            <h2 class="text-xl font-semibold">Solicitud #{{ $solicitud->ID_SOLICITUD }}</h2>
            @php
                $badgeFactura = match($solicitud->ESTADO_FACTURA) {
                    'facturada'  => 'badge-success',
                    'prefactura' => 'badge-info',
                    default      => 'badge-ghost',
                };
            @endphp
            <span class="badge {{ $badgeFactura }}">{{ ucfirst($solicitud->ESTADO_FACTURA) }}</span>
        </div>
    </x-slot>

    <x-alert />

    {{-- Navegación de pestañas --}}
    <div role="tablist" class="tabs tabs-boxed mb-4">
        <a href="{{ route('admin.solicitudes.show', $solicitud) }}" role="tab" class="tab">Datos</a>
        <a href="{{ route('admin.solicitudes.envio.show', $solicitud) }}" role="tab" class="tab">Envío</a>
        <a href="{{ route('admin.solicitudes.devolucion.show', $solicitud) }}" role="tab" class="tab">Devolución</a>
        <a href="{{ route('admin.solicitudes.facturacion.show', $solicitud) }}" role="tab" class="tab tab-active">Facturación</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- ═══════════ COLUMNA 1/3: DATOS DE FACTURACIÓN + ARCHIVO ═══════════ --}}
        <div class="lg:col-span-1 flex flex-col gap-4">

            {{-- DATOS DE FACTURACIÓN --}}
            <div x-data="{ editando: false }" class="card bg-base-100 shadow h-fit">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title text-base">Datos de facturación</h3>
                        <button type="button" class="btn btn-outline btn-info btn-xs" x-show="!editando" @click="editando = true">
                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                            Editar
                        </button>
                    </div>

                    {{-- SOLO LECTURA --}}
                    <div x-show="!editando" class="text-sm space-y-3">
                        <div>
                            <span class="text-xs text-base-content/50 block">Estado de factura</span>
                            <span class="font-medium">{{ ucfirst($solicitud->ESTADO_FACTURA) }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Fecha de facturación</span>
                            <span class="font-medium">{{ $solicitud->FACTURACION_FECHA?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Días de crédito</span>
                            <span class="font-medium">{{ $solicitud->FACTURACION_DIAS_CREDITO ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Notas</span>
                            <span class="font-medium">{{ $solicitud->FACTURACION_NOTAS ?: '—' }}</span>
                        </div>
                        <div class="divider my-0"></div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Importe</span>
                            <span class="font-medium">{{ $solicitud->IMPORTE_FACTURA ? '$' . number_format($solicitud->IMPORTE_FACTURA, 2) : '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Vencimiento de cobranza</span>
                            <span class="font-medium">{{ $solicitud->FECHA_VENCIMIENTO_COBRANZA?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/50 block">Estatus de cobranza</span>
                            @php $ec = $solicitud->estadoCobranza(); @endphp
                            <span class="badge badge-sm {{ $ec['class'] }}">{{ $ec['label'] }}</span>
                        </div>
                    </div>

                    {{-- FORMULARIO --}}
                    <form x-show="editando" method="POST" action="{{ route('admin.solicitudes.facturacion.update', $solicitud) }}">
                        @csrf @method('PATCH')

                        <div class="form-control mb-3">
                            <label class="label py-1"><span class="label-text text-xs">Estado de factura *</span></label>
                            <select name="ESTADO_FACTURA" class="select select-bordered select-sm w-full @error('ESTADO_FACTURA') select-error @enderror" required>
                                <option value="pendiente" {{ old('ESTADO_FACTURA', $solicitud->ESTADO_FACTURA) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="prefactura" {{ old('ESTADO_FACTURA', $solicitud->ESTADO_FACTURA) === 'prefactura' ? 'selected' : '' }}>Prefactura</option>
                                <option value="facturada" {{ old('ESTADO_FACTURA', $solicitud->ESTADO_FACTURA) === 'facturada' ? 'selected' : '' }}>Facturada</option>
                            </select>
                            @error('ESTADO_FACTURA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control mb-3">
                            <label class="label py-1"><span class="label-text text-xs">Fecha de facturación</span></label>
                            <input type="date" name="FACTURACION_FECHA"
                                value="{{ old('FACTURACION_FECHA', optional($solicitud->FACTURACION_FECHA)->format('Y-m-d')) }}"
                                class="input input-bordered input-sm w-full" />
                        </div>

                        <div class="form-control mb-3">
                            <label class="label py-1"><span class="label-text text-xs">Días de crédito</span></label>
                            <input type="number" min="0" name="FACTURACION_DIAS_CREDITO"
                                value="{{ old('FACTURACION_DIAS_CREDITO', $solicitud->FACTURACION_DIAS_CREDITO) }}"
                                class="input input-bordered input-sm w-full" />
                        </div>

                        <div class="form-control mb-4">
                            <label class="label py-1"><span class="label-text text-xs">Notas</span></label>
                            <textarea name="FACTURACION_NOTAS" rows="2" class="textarea textarea-bordered textarea-sm w-full">{{ old('FACTURACION_NOTAS', $solicitud->FACTURACION_NOTAS) }}</textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Guardar cambios</button>
                            <button type="button" class="btn btn-ghost btn-sm" @click="editando = false">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ARCHIVO DE FACTURA --}}
            <div class="card bg-base-100 shadow h-fit">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Archivo de factura</h3>
                    <form method="POST" action="{{ route('admin.solicitudes.factura', $solicitud) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-control mb-3">
                            <label class="label py-1"><span class="label-text text-xs">Folio de factura</span></label>
                            <input type="text" name="FOLIO_FACTURA"
                                value="{{ old('FOLIO_FACTURA', $solicitud->FOLIO_FACTURA) }}"
                                class="input input-bordered input-sm w-full @error('FOLIO_FACTURA') input-error @enderror" />
                            @error('FOLIO_FACTURA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control mb-3">
                            <label class="label py-1"><span class="label-text text-xs">Importe *</span></label>
                            <input type="number" step="0.01" min="0" name="IMPORTE_FACTURA"
                                value="{{ old('IMPORTE_FACTURA', $solicitud->IMPORTE_FACTURA) }}"
                                class="input input-bordered input-sm w-full @error('IMPORTE_FACTURA') input-error @enderror" required />
                            @error('IMPORTE_FACTURA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control mb-3">
                            <label class="label py-1"><span class="label-text text-xs">PDF</span></label>
                            <input type="file" name="FACTURA_PDF" accept=".pdf" class="file-input file-input-bordered file-input-sm w-full" />
                            @if($solicitud->FACTURA_PDF)
                                <a href="{{ Storage::url($solicitud->FACTURA_PDF) }}" target="_blank" class="link link-primary text-xs mt-1">Ver PDF actual</a>
                            @endif
                        </div>

                        <div class="form-control mb-4">
                            <label class="label py-1"><span class="label-text text-xs">XML</span></label>
                            <input type="file" name="FACTURA_XML" accept=".xml" class="file-input file-input-bordered file-input-sm w-full" />
                            @if($solicitud->FACTURA_XML)
                                <a href="{{ Storage::url($solicitud->FACTURA_XML) }}" target="_blank" class="link link-primary text-xs mt-1">Ver XML actual</a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Guardar factura</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════════ COLUMNA 2/3: PAGOS ═══════════ --}}
        <div class="lg:col-span-2" x-data="{ modalPago: false }">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title text-base">Pagos registrados</h3>
                        @if($solicitud->ESTADO_FACTURA === 'facturada')
                            <button type="button" class="btn btn-primary btn-sm gap-1" @click="modalPago = true">
                                <x-heroicon-o-plus class="w-4 h-4" />
                                Agregar pago
                            </button>
                        @endif
                    </div>

                    @if($solicitud->pagos->isEmpty())
                        @if($solicitud->ESTADO_FACTURA !== 'facturada')
                            <div class="alert alert-warning text-sm">
                                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                                <span>
                                    Esta solicitud aún no tiene factura registrada. Para poder capturar pagos, primero
                                    completa el <strong>Importe</strong> y guarda el archivo de factura en la sección
                                    "Archivo de factura", lo que marcará el estado como <strong>Facturada</strong>.
                                </span>
                            </div>
                        @else
                            <div class="alert alert-info text-sm">
                                <x-heroicon-o-information-circle class="w-5 h-5" />
                                <span>
                                    Aún no hay pagos registrados para esta solicitud. Usa el botón
                                    <strong>"Agregar pago"</strong> de la esquina superior para capturar el primer pago
                                    (fecha, importe, forma de pago y notas opcionales).
                                </span>
                            </div>
                        @endif
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th class="text-right">Importe</th>
                                        <th>Forma de pago</th>
                                        <th>Notas</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($solicitud->pagos as $pago)
                                        <tr>
                                            <td>{{ $pago->FECHA_PAGO?->format('d/m/Y') }}</td>
                                            <td class="text-right font-mono text-success">${{ number_format($pago->IMPORTE, 2) }}</td>
                                            <td class="text-sm">{{ $pago->FORMA_PAGO ?: '—' }}</td>
                                            <td class="text-sm text-base-content/60">{{ $pago->NOTAS ?: '—' }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.solicitudes.pagos.destroy', [$solicitud, $pago]) }}"
                                                    onsubmit="return confirm('¿Eliminar este pago?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-ghost btn-xs text-error">
                                                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-semibold">
                                        <td>Total pagado</td>
                                        <td class="text-right font-mono text-success">${{ number_format($solicitud->totalPagado(), 2) }}</td>
                                        <td colspan="3">Saldo: <span class="{{ $solicitud->saldoPendiente() > 0 ? 'text-error' : 'text-success' }}">${{ number_format($solicitud->saldoPendiente(), 2) }}</span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- MODAL: AGREGAR PAGO --}}
            <div x-show="modalPago" x-cloak class="modal" :class="{ 'modal-open': modalPago }">
                <div class="modal-box max-w-sm">
                    <h3 class="font-bold text-lg mb-4">Registrar pago</h3>
                    <form method="POST" action="{{ route('admin.solicitudes.pagos.store', $solicitud) }}"
                        x-data="pagoForm({{ $solicitud->saldoPendiente() }})">
                        @csrf
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Fecha de pago *</span></label>
                            <input type="date" name="FECHA_PAGO" value="{{ old('FECHA_PAGO', now()->format('Y-m-d')) }}"
                                class="input input-bordered @error('FECHA_PAGO') input-error @enderror" required />
                            @error('FECHA_PAGO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Importe *</span></label>
                            <label class="input input-bordered flex items-center gap-1" :class="{ 'input-error': excede }">
                                <span class="text-base-content/50">$</span>
                                <input type="text" inputmode="decimal" x-model="importeDisplay"
                                    @input="formatearImporte($event)" placeholder="0.00" class="grow" />
                            </label>
                            <input type="hidden" name="IMPORTE" :value="importeRaw ?? ''" />
                            <p class="text-xs text-base-content/50 mt-1">
                                Saldo pendiente: <span x-text="formatoMoneda(saldoPendiente)"></span>
                            </p>
                            <p class="text-error text-xs mt-1" x-show="excede" x-cloak>
                                El importe no puede exceder el saldo pendiente.
                            </p>
                            @error('IMPORTE')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Forma de pago</span></label>
                            <input type="text" name="FORMA_PAGO" value="{{ old('FORMA_PAGO') }}"
                                placeholder="Ej: Transferencia, Efectivo, Cheque"
                                class="input input-bordered @error('FORMA_PAGO') input-error @enderror" />
                            @error('FORMA_PAGO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Notas</span></label>
                            <textarea name="NOTAS" rows="2" class="textarea textarea-bordered">{{ old('NOTAS') }}</textarea>
                        </div>
                        <div class="modal-action">
                            <button type="button" class="btn btn-ghost btn-sm" @click="modalPago = false">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm"
                                :disabled="excede || !importeRaw || importeRaw <= 0">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
    function pagoForm(saldoPendiente) {
        return {
            saldoPendiente: parseFloat(saldoPendiente) || 0,
            importeRaw: null,
            importeDisplay: '',

            get excede() {
                return this.importeRaw !== null && this.importeRaw > this.saldoPendiente;
            },

            formatoMoneda(valor) {
                return '$' + Number(valor).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            formatearImporte(e) {
                let valor = e.target.value.replace(/[^0-9.]/g, '');

                const partes = valor.split('.');
                if (partes.length > 2) {
                    valor = partes[0] + '.' + partes.slice(1).join('');
                }

                const [entero, decimal] = valor.split('.');
                const enteroFormateado = entero ? parseInt(entero, 10).toLocaleString('es-MX') : '';

                this.importeDisplay = decimal !== undefined
                    ? `${enteroFormateado}.${decimal.slice(0, 2)}`
                    : enteroFormateado;

                this.importeRaw = valor && valor !== '.' ? parseFloat(valor) : null;
            },
        }
    }
</script>
</x-app-layout>