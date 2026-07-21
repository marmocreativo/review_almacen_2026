<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <h2 class="text-lg font-semibold truncate">
                    Solicitud <span class="font-mono text-primary">#{{ $solicitud->ID_SOLICITUD }}</span>
                </h2>
            </div>
            {{-- Acciones desktop --}}
            <div class="hidden md:flex gap-2">
                @if($solicitud->isPendiente())
                    <a href="{{ route('admin.solicitudes.edit', $solicitud) }}" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-pencil class="w-4 h-4" />
                        Editar
                    </a>
                @endif
                <a href="{{ route('admin.solicitudes.pdf', $solicitud) }}" class="btn btn-outline btn-sm gap-1" target="_blank">
                    <x-heroicon-o-document-arrow-down class="w-4 h-4" />
                    Carta de envío
                </a>
                <button onclick="document.getElementById('modal-email').showModal()" class="btn btn-outline btn-sm gap-1">
                    <x-heroicon-o-envelope class="w-4 h-4" />
                    Correo
                </button>
                <button onclick="document.getElementById('modal-factura').showModal()" class="btn btn-outline btn-sm gap-1">
                    <x-heroicon-o-document-text class="w-4 h-4" />
                    Facturación
                </button>
                @if($solicitud->ESTADO_FACTURA === 'facturada')
                    <button onclick="document.getElementById('modal-cobranza').showModal()" class="btn btn-outline btn-sm gap-1">
                        <x-heroicon-o-banknotes class="w-4 h-4" />
                        Cobranza
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- ===================== TOOLBAR MÓVIL ===================== --}}
    <div class="md:hidden bg-base-100 border-b border-base-300 px-3 py-2 flex gap-2 overflow-x-auto">
        @if($solicitud->isPendiente())
            <a href="{{ route('admin.solicitudes.edit', $solicitud) }}" class="btn btn-primary btn-sm gap-1 shrink-0">
                <x-heroicon-o-pencil class="w-4 h-4" />
                Editar
            </a>
        @elseif($solicitud->isEnviada())
            <a href="{{ route('admin.solicitudes.edit', $solicitud) }}" class="btn btn-info btn-sm gap-1 shrink-0">
                <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                Retornar
            </a>
        @endif
        <a href="{{ route('admin.solicitudes.pdf', $solicitud) }}" class="btn btn-outline btn-sm gap-1 shrink-0" target="_blank">
            <x-heroicon-o-document-arrow-down class="w-4 h-4" />
            Carta de envío
        </a>
        <button onclick="document.getElementById('modal-email').showModal()" class="btn btn-outline btn-sm gap-1 shrink-0">
            <x-heroicon-o-envelope class="w-4 h-4" />
            Correo
        </button>
        <button onclick="document.getElementById('modal-factura').showModal()" class="btn btn-outline btn-sm gap-1 shrink-0">
            <x-heroicon-o-document-text class="w-4 h-4" />
            Facturación
        </button>
        @if($solicitud->ESTADO_FACTURA === 'facturada')
            <button onclick="document.getElementById('modal-cobranza').showModal()" class="btn btn-outline btn-sm gap-1 shrink-0">
                <x-heroicon-o-banknotes class="w-4 h-4" />
                Cobranza
            </button>
        @endif
    </div>

    <x-alert />

    {{-- ===================== BLOQUES DE INFO ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- BLOQUE 1: Empresa, sede y contacto --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body py-4">
                <h3 class="text-xs font-medium text-base-content/50 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                    <x-heroicon-o-building-office-2 class="w-4 h-4" />
                    Empresa y sede
                </h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-base-content/40 uppercase tracking-wide">Empresa</p>
                        <p class="font-semibold">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/40 uppercase tracking-wide">Sede</p>
                        <p>{{ $solicitud->sede?->nombre ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/40 uppercase tracking-wide">Contacto</p>
                        <p>{{ $solicitud->contacto?->nombre ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- BLOQUE 2: Fecha, responsable, dirección, horario --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body py-4">
                <h3 class="text-xs font-medium text-base-content/50 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                    <x-heroicon-o-calendar-days class="w-4 h-4" />
                    Logística
                </h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-base-content/40 uppercase tracking-wide">Fecha solicitud</p>
                        <p>{{ $solicitud->FECHA_SOLICITUD?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/40 uppercase tracking-wide">Responsable</p>
                        <p class="font-medium">{{ $solicitud->RESPONSABLE_NOMBRE }}</p>
                        @if($solicitud->RESPONSABLE_CORREO)
                            <p class="text-xs text-base-content/50">{{ $solicitud->RESPONSABLE_CORREO }}</p>
                        @endif
                        @if($solicitud->RESPONSABLE_TELEFONO)
                            <p class="text-xs text-base-content/50">Tel: {{ $solicitud->RESPONSABLE_TELEFONO }}</p>
                        @endif
                        @if($solicitud->RESPONSABLE_CELULAR)
                            <p class="text-xs text-base-content/50">Cel: {{ $solicitud->RESPONSABLE_CELULAR }}</p>
                        @endif
                    </div>
                    @if($solicitud->HORARIO_DE_ATENCION)
                        <div>
                            <p class="text-xs text-base-content/40 uppercase tracking-wide">Horario de atención</p>
                            <p>{{ $solicitud->HORARIO_DE_ATENCION }}</p>
                        </div>
                    @endif
                    @if($solicitud->DIRECCION_ENVIO)
                        <div>
                            <p class="text-xs text-base-content/40 uppercase tracking-wide">Dirección de envío</p>
                            <p>{{ $solicitud->DIRECCION_ENVIO }}</p>
                        </div>
                    @endif
                    @if($solicitud->OBSERVACIONES)
                        <div>
                            <p class="text-xs text-base-content/40 uppercase tracking-wide">Observaciones</p>
                            <p class="text-base-content/70 italic">{{ $solicitud->OBSERVACIONES }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- BLOQUE 3: Estado, sesiones, exámenes, factura --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body py-4">
                <h3 class="text-xs font-medium text-base-content/50 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                    <x-heroicon-o-chart-bar class="w-4 h-4" />
                    Estado y contadores
                </h3>
                <div class="space-y-3 text-sm">

                    {{-- Progreso de estado --}}
                    <div>
                        <p class="text-xs text-base-content/40 uppercase tracking-wide mb-2">Estado solicitud</p>
                        @php
                            $estado = $solicitud->ESTADO_SOLICITUD;
                            $facturada = $solicitud->ESTADO_FACTURA === 'facturada';
                            $cobrada   = $facturada && $solicitud->saldoPendiente() <= 0;

                            $pasos = [
                                ['label' => 'Pendiente',   'activo' => true,                              'completo' => in_array($estado, ['enviada','retornada'])],
                                ['label' => 'Enviada',     'activo' => $estado === 'enviada',              'completo' => $estado === 'retornada'],
                                ['label' => 'Retorno',     'activo' => $estado === 'retornada',            'completo' => $facturada],
                                ['label' => 'Facturación', 'activo' => $facturada && !$cobrada,            'completo' => $cobrada],
                                ['label' => 'Cobranza',    'activo' => $cobrada,                           'completo' => $cobrada],
                            ];
                        @endphp
                        <div class="flex items-center">
                            @foreach($pasos as $i => $paso)
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center
                                        {{ ($paso['completo'] || $paso['activo']) ? 'bg-primary border-primary' : 'border-base-300' }}">
                                        @if($paso['completo'])
                                            <x-heroicon-s-check class="w-2 h-2 text-primary-content" />
                                        @endif
                                    </div>
                                    <span class="text-[11px] whitespace-nowrap {{ $paso['activo'] ? 'text-primary font-medium' : 'text-base-content/40' }}">{{ $paso['label'] }}</span>
                                </div>
                                @if(!$loop->last)
                                    <div class="flex-1 h-0.5 mb-4 mx-1 {{ $paso['completo'] ? 'bg-primary' : 'bg-base-300' }}"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="divider my-1"></div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-base-200/50 rounded-lg p-2 text-center">
                            <p class="text-xs text-base-content/40">Sesiones</p>
                            <p class="font-semibold text-sm">{{ ucfirst($solicitud->SESIONES_SIMULTANEAS) }}</p>
                        </div>
                        <div class="bg-base-200/50 rounded-lg p-2 text-center">
                            <p class="text-xs text-base-content/40">Exámenes</p>
                            <p class="font-bold font-mono text-xl">{{ $solicitud->CANTIDAD_EXAMENES }}</p>
                            <p class="text-xs text-base-content/40">{{ $solicitud->CANTIDAD_EXAMENES_APLICADOS }} aplicados</p>
                        </div>
                    </div>

                    <div class="divider my-1"></div>

                    {{-- Factura --}}
                    <div>
                        <p class="text-xs text-base-content/40 uppercase tracking-wide mb-1">Factura</p>
                        <span class="badge {{ $solicitud->ESTADO_FACTURA === 'facturada' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($solicitud->ESTADO_FACTURA) }}
                        </span>
                        @if($solicitud->IMPORTE_FACTURA)
                            <p class="text-xl font-bold font-mono mt-1">${{ number_format($solicitud->IMPORTE_FACTURA, 2) }}</p>
                        @endif
                        @if($solicitud->FACTURA_PDF)
                            <a href="{{ Storage::url($solicitud->FACTURA_PDF) }}" target="_blank"
                                class="btn btn-xs btn-outline mt-2 gap-1">
                                <x-heroicon-o-document-arrow-down class="w-3 h-3" />
                                PDF
                            </a>
                        @endif
                        @if($solicitud->FACTURA_XML)
                            <a href="{{ Storage::url($solicitud->FACTURA_XML) }}" target="_blank"
                                class="btn btn-xs btn-outline mt-2 gap-1">
                                <x-heroicon-o-code-bracket class="w-3 h-3" />
                                XML
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== EXÁMENES ===================== --}}
    @forelse($solicitud->examenes as $examen)
        <div class="card bg-base-100 shadow mb-4">
            <div class="card-body">

                {{-- Cabecera del examen --}}
                <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <x-heroicon-o-document-text class="w-5 h-5 text-primary shrink-0" />
                        <h3 class="font-semibold text-base truncate">{{ $examen->EXAMEN }}</h3>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs shrink-0">
                        <span class="text-base-content/50">
                            Cant: <strong class="font-mono">{{ $examen->CANTIDAD }}</strong>
                        </span>
                        @if($examen->FECHA)
                            <span class="text-base-content/50">
                                {{ $examen->FECHA->format('d/m/Y') }}
                            </span>
                        @endif
                        <span class="badge badge-sm
                            {{ $examen->ESTADO === 'pendiente' ? 'badge-warning' :
                               ($examen->ESTADO === 'enviado'  ? 'badge-info'    : 'badge-success') }}">
                            {{ ucfirst($examen->ESTADO) }}
                        </span>
                    </div>
                </div>

                @if($examen->articulos->isNotEmpty())
                    {{-- DESKTOP: tabla --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="table table-sm table-zebra">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Serie</th>
                                    <th>Nombre</th>
                                    <th>Formato</th>
                                    <th>Estado</th>
                                    <th class="text-center">Enviados</th>
                                    <th class="text-center">Retornados</th>
                                    <th class="text-center">Destrucción</th>
                                    <th class="text-center">Perdidos</th>
                                    <th>Candidato</th>
                                    <th>F. Retorno</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examen->articulos as $sa)
                                    <tr>
                                        <td class="font-mono text-xs">{{ $sa->FOLIO }}</td>
                                        <td class="font-mono text-xs">{{ $sa->SERIE ?: '—' }}</td>
                                        <td>{{ $sa->NOMBRE }}</td>
                                        <td class="text-xs">{{ $sa->FORMATO ?: '—' }}</td>
                                        <td>
                                            <span class="badge badge-sm
                                                {{ $sa->ESTADO === 'retornado'  ? 'badge-success' :
                                                   ($sa->ESTADO === 'solicitud' ? 'badge-warning'  : 'badge-ghost') }}">
                                                {{ ucfirst($sa->ESTADO) }}
                                            </span>
                                        </td>
                                        <td class="text-center font-mono">{{ $sa->CANTIDAD_ENVIADA }}</td>
                                        <td class="text-center font-mono">{{ $sa->CANTIDAD_A_ALMACEN }}</td>
                                        <td class="text-center font-mono">{{ $sa->CANTIDAD_A_DESTRUCCION }}</td>
                                        <td class="text-center font-mono">{{ $sa->CANTIDAD_PERDIDOS }}</td>
                                        <td class="text-xs">{{ $sa->NOMBRE_CANDIDATO ?: '—' }}</td>
                                        <td class="text-xs">{{ $sa->FECHA_RETORNO?->format('d/m/Y') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- MÓVIL: cards por artículo --}}
                    <div class="md:hidden space-y-2">
                        @foreach($examen->articulos as $sa)
                            <div class="bg-base-200/40 rounded-lg p-3">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="min-w-0">
                                        <p class="font-medium text-sm truncate">{{ $sa->NOMBRE }}</p>
                                        <div class="flex gap-2 mt-0.5">
                                            <span class="font-mono text-xs text-base-content/50">{{ $sa->FOLIO }}</span>
                                            @if($sa->SERIE)
                                                <span class="font-mono text-xs text-base-content/40">{{ $sa->SERIE }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge badge-sm shrink-0
                                        {{ $sa->ESTADO === 'retornado'  ? 'badge-success' :
                                           ($sa->ESTADO === 'solicitud' ? 'badge-warning'  : 'badge-ghost') }}">
                                        {{ ucfirst($sa->ESTADO) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-4 gap-1 text-center text-xs">
                                    <div class="bg-base-100 rounded p-1.5">
                                        <p class="text-base-content/40 leading-none mb-0.5">Env.</p>
                                        <p class="font-mono font-bold">{{ $sa->CANTIDAD_ENVIADA }}</p>
                                    </div>
                                    <div class="bg-base-100 rounded p-1.5">
                                        <p class="text-base-content/40 leading-none mb-0.5">Ret.</p>
                                        <p class="font-mono font-bold">{{ $sa->CANTIDAD_A_ALMACEN }}</p>
                                    </div>
                                    <div class="bg-base-100 rounded p-1.5">
                                        <p class="text-base-content/40 leading-none mb-0.5">Dest.</p>
                                        <p class="font-mono font-bold">{{ $sa->CANTIDAD_A_DESTRUCCION }}</p>
                                    </div>
                                    <div class="bg-base-100 rounded p-1.5">
                                        <p class="text-base-content/40 leading-none mb-0.5">Perd.</p>
                                        <p class="font-mono font-bold">{{ $sa->CANTIDAD_PERDIDOS }}</p>
                                    </div>
                                </div>

                                @if($sa->NOMBRE_CANDIDATO || $sa->FECHA_RETORNO)
                                    <div class="flex gap-3 mt-2 text-xs text-base-content/50">
                                        @if($sa->NOMBRE_CANDIDATO)
                                            <span class="flex items-center gap-1">
                                                <x-heroicon-o-user class="w-3 h-3" />
                                                {{ $sa->NOMBRE_CANDIDATO }}
                                            </span>
                                        @endif
                                        @if($sa->FECHA_RETORNO)
                                            <span class="flex items-center gap-1">
                                                <x-heroicon-o-calendar-days class="w-3 h-3" />
                                                {{ $sa->FECHA_RETORNO->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-base-content/40 italic">Sin artículos registrados en este examen.</p>
                @endif
            </div>
        </div>
    @empty
        <div class="alert">
            <x-heroicon-o-information-circle class="w-5 h-5" />
            <span>Esta solicitud no tiene exámenes registrados.</span>
        </div>
    @endforelse

    {{-- ===================== MODAL EMAIL ===================== --}}
    <dialog id="modal-email" class="modal">
        <div class="modal-box w-11/12 max-w-sm">
            <h3 class="font-bold text-lg mb-4">Enviar por correo</h3>
            <form method="POST" action="{{ route('admin.solicitudes.email', $solicitud) }}">
                @csrf
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Correo destinatario</span></label>
                    <input type="email" name="email_destino"
                        value="{{ $solicitud->RESPONSABLE_CORREO }}"
                        class="input input-bordered" required />
                </div>
                <p class="text-xs text-base-content/50 mb-4">
                    Se enviará un resumen ejecutivo con el PDF adjunto.
                </p>
                <div class="modal-action">
                    <button type="submit" class="btn btn-primary">
                        <x-heroicon-o-paper-airplane class="w-4 h-4" />
                        Enviar
                    </button>
                    <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    {{-- ===================== MODAL FACTURA ===================== --}}
    <dialog id="modal-factura" class="modal">
        <div class="modal-box w-11/12 max-w-md">
            <h3 class="font-bold text-lg mb-4">Adjuntar factura</h3>
            <form method="POST" action="{{ route('admin.solicitudes.factura', $solicitud) }}"
                enctype="multipart/form-data">
                @csrf
                <div class="form-control mb-3">
                    <label class="label"><span class="label-text">Importe factura *</span></label>
                    <input type="number" step="0.01" min="0" name="IMPORTE_FACTURA"
                        value="{{ $solicitud->IMPORTE_FACTURA }}"
                        class="input input-bordered" required />
                </div>
                <div class="form-control mb-3">
                    <label class="label">
                        <span class="label-text">Factura PDF</span>
                        @if($solicitud->FACTURA_PDF)
                            <span class="label-text-alt text-success">✓ Ya tiene archivo</span>
                        @endif
                    </label>
                    <input type="file" name="FACTURA_PDF" accept=".pdf"
                        class="file-input file-input-bordered file-input-sm" />
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Factura XML</span>
                        @if($solicitud->FACTURA_XML)
                            <span class="label-text-alt text-success">✓ Ya tiene archivo</span>
                        @endif
                    </label>
                    <input type="file" name="FACTURA_XML" accept=".xml"
                        class="file-input file-input-bordered file-input-sm" />
                </div>
                <div class="modal-action">
                    <button type="submit" class="btn btn-primary gap-1">
                        <x-heroicon-o-document-arrow-up class="w-4 h-4" />
                        Guardar factura
                    </button>
                    <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>

    @if($solicitud->ESTADO_FACTURA === 'facturada')
    {{-- ===================== MODAL COBRANZA ===================== --}}
    <dialog id="modal-cobranza" class="modal">
        <div class="modal-box w-11/12 max-w-lg">
            <h3 class="font-bold text-lg mb-1">Cobranza</h3>
            <p class="text-sm text-base-content/50 mb-4">
                Factura: <strong>${{ number_format($solicitud->IMPORTE_FACTURA, 2) }}</strong>
                — Pagado: <strong class="text-success">${{ number_format($solicitud->totalPagado(), 2) }}</strong>
                — Saldo: <strong class="{{ $solicitud->saldoPendiente() > 0 ? 'text-error' : '' }}">${{ number_format($solicitud->saldoPendiente(), 2) }}</strong>
            </p>

            {{-- Fecha de vencimiento --}}
            <form method="POST" action="{{ route('admin.solicitudes.vencimiento-cobranza', $solicitud) }}" class="flex gap-2 items-end mb-4 pb-4 border-b border-base-200">
                @csrf @method('PATCH')
                <div class="form-control flex-1">
                    <label class="label py-1"><span class="label-text text-xs">Fecha de vencimiento</span></label>
                    <input type="date" name="FECHA_VENCIMIENTO_COBRANZA"
                        value="{{ $solicitud->FECHA_VENCIMIENTO_COBRANZA?->format('Y-m-d') }}"
                        class="input input-bordered input-sm w-full" required />
                </div>
                <button type="submit" class="btn btn-outline btn-sm">Guardar</button>
            </form>

            {{-- Lista de pagos --}}
            <div class="mb-4">
                <h4 class="text-xs font-medium text-base-content/50 uppercase tracking-wide mb-2">Pagos registrados</h4>
                @forelse($solicitud->pagos as $pago)
                    <div class="flex items-center justify-between gap-2 py-2 border-b border-base-100 text-sm">
                        <div class="min-w-0 flex-1">
                            <p class="font-mono font-semibold">${{ number_format($pago->IMPORTE, 2) }}</p>
                            <p class="text-xs text-base-content/50">{{ $pago->FECHA_PAGO->format('d/m/Y') }}</p>
                            @if($pago->NOTAS)
                                <p class="text-xs text-base-content/40 truncate">{{ $pago->NOTAS }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.solicitudes.pagos.destroy', [$solicitud, $pago]) }}"
                            onsubmit="return confirm('¿Eliminar este pago?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-ghost text-error">
                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-base-content/40 italic">Sin pagos registrados todavía.</p>
                @endforelse
            </div>

            {{-- Agregar pago --}}
            <form method="POST" action="{{ route('admin.solicitudes.pagos.store', $solicitud) }}">
                @csrf
                <h4 class="text-xs font-medium text-base-content/50 uppercase tracking-wide mb-2">Registrar nuevo pago</h4>
                <div class="grid grid-cols-2 gap-3">
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs">Fecha *</span></label>
                        <input type="date" name="FECHA_PAGO" value="{{ now()->format('Y-m-d') }}"
                            class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs">Importe *</span></label>
                        <input type="number" step="0.01" min="0.01" name="IMPORTE"
                            class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control col-span-2">
                        <label class="label py-1"><span class="label-text text-xs">Notas</span></label>
                        <input type="text" name="NOTAS" class="input input-bordered input-sm w-full" />
                    </div>
                </div>
                <div class="modal-action">
                    <button type="submit" class="btn btn-primary gap-1">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Agregar pago
                    </button>
                    <form method="dialog"><button class="btn btn-ghost">Cerrar</button></form>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
    </dialog>
    @endif

</x-app-layout>