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

    {{-- ═══════════ DATOS DE FACTURACIÓN ═══════════ --}}
    <div x-data="{ editando: false }" class="card bg-base-100 shadow mb-4">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="card-title text-base">Datos de facturación</h3>
                <button type="button" class="btn btn-outline btn-info btn-sm" x-show="!editando" @click="editando = true">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    Editar
                </button>
            </div>

            {{-- SOLO LECTURA --}}
            <dl x-show="!editando" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-xs text-base-content/50">Estado de factura</dt>
                    <dd class="font-medium">{{ ucfirst($solicitud->ESTADO_FACTURA) }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Fecha de facturación</dt>
                    <dd class="font-medium">{{ $solicitud->FACTURACION_FECHA?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Días de crédito</dt>
                    <dd class="font-medium">{{ $solicitud->FACTURACION_DIAS_CREDITO ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-3">
                    <dt class="text-xs text-base-content/50">Notas</dt>
                    <dd class="font-medium">{{ $solicitud->FACTURACION_NOTAS ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Importe</dt>
                    <dd class="font-medium">{{ $solicitud->IMPORTE_FACTURA ? '$' . number_format($solicitud->IMPORTE_FACTURA, 2) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Vencimiento de cobranza</dt>
                    <dd class="font-medium">{{ $solicitud->FECHA_VENCIMIENTO_COBRANZA?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-base-content/50">Estatus de cobranza</dt>
                    @php $ec = $solicitud->estadoCobranza(); @endphp
                    <dd><span class="badge badge-sm {{ $ec['class'] }}">{{ $ec['label'] }}</span></dd>
                </div>
            </dl>

            {{-- FORMULARIO --}}
            <form x-show="editando" method="POST" action="{{ route('admin.solicitudes.facturacion.update', $solicitud) }}">
                @csrf @method('PATCH')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Estado de factura *</span></label>
                        <select name="ESTADO_FACTURA" class="select select-bordered @error('ESTADO_FACTURA') select-error @enderror" required>
                            <option value="pendiente" {{ old('ESTADO_FACTURA', $solicitud->ESTADO_FACTURA) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="prefactura" {{ old('ESTADO_FACTURA', $solicitud->ESTADO_FACTURA) === 'prefactura' ? 'selected' : '' }}>Prefactura</option>
                            <option value="facturada" {{ old('ESTADO_FACTURA', $solicitud->ESTADO_FACTURA) === 'facturada' ? 'selected' : '' }}>Facturada</option>
                        </select>
                        @error('ESTADO_FACTURA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Fecha de facturación</span></label>
                        <input type="date" name="FACTURACION_FECHA"
                            value="{{ old('FACTURACION_FECHA', optional($solicitud->FACTURACION_FECHA)->format('Y-m-d')) }}"
                            class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Días de crédito</span></label>
                        <input type="number" min="0" name="FACTURACION_DIAS_CREDITO"
                            value="{{ old('FACTURACION_DIAS_CREDITO', $solicitud->FACTURACION_DIAS_CREDITO) }}"
                            class="input input-bordered" />
                    </div>
                    <div class="form-control sm:col-span-3">
                        <label class="label"><span class="label-text">Notas</span></label>
                        <textarea name="FACTURACION_NOTAS" rows="2" class="textarea textarea-bordered">{{ old('FACTURACION_NOTAS', $solicitud->FACTURACION_NOTAS) }}</textarea>
                    </div>
                </div>
                <div class="flex gap-2 mt-6">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <button type="button" class="btn btn-ghost" @click="editando = false">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════ ARCHIVO DE FACTURA ═══════════ --}}
    <div class="card bg-base-100 shadow mb-4">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Archivo de factura</h3>
            <form method="POST" action="{{ route('admin.solicitudes.factura', $solicitud) }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Importe *</span></label>
                        <input type="number" step="0.01" min="0" name="IMPORTE_FACTURA"
                            value="{{ old('IMPORTE_FACTURA', $solicitud->IMPORTE_FACTURA) }}"
                            class="input input-bordered @error('IMPORTE_FACTURA') input-error @enderror" required />
                        @error('IMPORTE_FACTURA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">PDF</span></label>
                        <input type="file" name="FACTURA_PDF" accept=".pdf" class="file-input file-input-bordered" />
                        @if($solicitud->FACTURA_PDF)
                            <a href="{{ Storage::url($solicitud->FACTURA_PDF) }}" target="_blank" class="link link-primary text-xs mt-1">Ver PDF actual</a>
                        @endif
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">XML</span></label>
                        <input type="file" name="FACTURA_XML" accept=".xml" class="file-input file-input-bordered" />
                        @if($solicitud->FACTURA_XML)
                            <a href="{{ Storage::url($solicitud->FACTURA_XML) }}" target="_blank" class="link link-primary text-xs mt-1">Ver XML actual</a>
                        @endif
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm mt-4">Guardar factura</button>
            </form>
        </div>
    </div>

    {{-- ═══════════ PAGOS ═══════════ --}}
    <div class="card bg-base-100 shadow" x-data="{ modalPago: false }">
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

            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th class="text-right">Importe</th>
                            <th>Notas</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitud->pagos as $pago)
                            <tr>
                                <td>{{ $pago->FECHA_PAGO?->format('d/m/Y') }}</td>
                                <td class="text-right font-mono text-success">${{ number_format($pago->IMPORTE, 2) }}</td>
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
                        @empty
                            <tr><td colspan="4" class="text-center text-base-content/50 py-6">Sin pagos registrados.</td></tr>
                        @endforelse
                    </tbody>
                    @if($solicitud->pagos->isNotEmpty())
                        <tfoot>
                            <tr class="font-semibold">
                                <td>Total pagado</td>
                                <td class="text-right font-mono text-success">${{ number_format($solicitud->totalPagado(), 2) }}</td>
                                <td colspan="2">Saldo: <span class="{{ $solicitud->saldoPendiente() > 0 ? 'text-error' : 'text-success' }}">${{ number_format($solicitud->saldoPendiente(), 2) }}</span></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            {{-- MODAL: AGREGAR PAGO --}}
            <div x-show="modalPago" x-cloak class="modal" :class="{ 'modal-open': modalPago }">
                <div class="modal-box max-w-sm">
                    <h3 class="font-bold text-lg mb-4">Registrar pago</h3>
                    <form method="POST" action="{{ route('admin.solicitudes.pagos.store', $solicitud) }}">
                        @csrf
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Fecha de pago *</span></label>
                            <input type="date" name="FECHA_PAGO" value="{{ old('FECHA_PAGO', now()->format('Y-m-d')) }}"
                                class="input input-bordered @error('FECHA_PAGO') input-error @enderror" required />
                            @error('FECHA_PAGO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text">Importe *</span></label>
                            <input type="number" step="0.01" min="0.01" name="IMPORTE" value="{{ old('IMPORTE') }}"
                                class="input input-bordered @error('IMPORTE') input-error @enderror" required />
                            @error('IMPORTE')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Notas</span></label>
                            <textarea name="NOTAS" rows="2" class="textarea textarea-bordered">{{ old('NOTAS') }}</textarea>
                        </div>
                        <div class="modal-action">
                            <button type="button" class="btn btn-ghost btn-sm" @click="modalPago = false">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>