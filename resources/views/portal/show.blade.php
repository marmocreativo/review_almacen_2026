<!DOCTYPE html>
<html lang="es" data-theme="rq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitud #{{ $solicitud->ID_SOLICITUD }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen p-4">
    <div class="max-w-2xl mx-auto py-6">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('portal.solicitudes.historial') }}" class="btn btn-ghost btn-sm btn-square">
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
        <p class="text-sm text-base-content/50 mb-6">
            Registrada el {{ $solicitud->FECHA_SOLICITUD?->format('d/m/Y H:i') }}
        </p>

        @if(session('success'))
            <div class="alert alert-success text-sm mb-4"><span>{{ session('success') }}</span></div>
        @endif

        <div class="card bg-base-100 shadow mb-4">
            <div class="card-body">
                <h3 class="card-title text-base mb-3">Datos generales</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-base-content/50">Cliente</dt>
                        <dd class="font-medium">{{ $solicitud->empresa?->nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Sede</dt>
                        <dd class="font-medium">{{ $solicitud->sede?->nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Zona de envío</dt>
                        <dd class="font-medium">
                            {{ $solicitud->ENVIO_ZONA === 'cdmx_area_metropolitana' ? 'CDMX / Área Metropolitana' : ($solicitud->ENVIO_ZONA === 'foraneo' ? 'Foráneo' : '—') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Fecha primer aplicación</dt>
                        <dd class="font-medium">{{ $solicitud->FECHA_PRIMERA_APLICACION?->format('d/m/Y') ?? '—' }}</dd>
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
                        <dt class="text-xs text-base-content/50">Responsable</dt>
                        <dd class="font-medium">{{ trim(($solicitud->RESPONSABLE_TITULO ? $solicitud->RESPONSABLE_TITULO . ' ' : '') . $solicitud->RESPONSABLE_NOMBRE) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Correo</dt>
                        <dd class="font-medium">{{ $solicitud->RESPONSABLE_CORREO ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Teléfono</dt>
                        <dd class="font-medium">{{ $solicitud->RESPONSABLE_TELEFONO ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Cantidad USB / CD</dt>
                        <dd class="font-medium">{{ $solicitud->CANTIDAD_USB ?? 0 }} / {{ $solicitud->CANTIDAD_CD ?? 0 }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-base-content/50">Dirección de envío</dt>
                        <dd class="font-medium">{{ $solicitud->DIRECCION_ENVIO ?: '—' }}</dd>
                    </div>
                    @if($solicitud->OBSERVACIONES)
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-base-content/50">Observaciones</dt>
                            <dd class="font-medium">{{ $solicitud->OBSERVACIONES }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title text-base mb-3">Exámenes solicitados ({{ $solicitud->examenes->count() }})</h3>
                @forelse($solicitud->examenes as $examen)
                    <div class="flex items-center justify-between py-2 border-b border-base-300 last:border-0">
                        <div>
                            <p class="font-medium text-sm">{{ $examen->EXAMEN }}</p>
                            <p class="text-xs text-base-content/50">{{ $examen->FECHA?->format('d/m/Y') }}</p>
                        </div>
                        <span class="badge badge-info badge-sm">{{ $examen->CANTIDAD }} candidatos</span>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50 text-center py-4">No se agregaron exámenes a esta solicitud.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>