<!DOCTYPE html>
<html lang="es" data-theme="rq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resumen — Solicitud #{{ $solicitud->ID_SOLICITUD }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen p-4">
    <div class="max-w-2xl mx-auto py-6">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-success/20 mb-3">
                <x-heroicon-o-check class="w-8 h-8 text-success" />
            </div>
            <h2 class="text-xl font-semibold">Solicitud #{{ $solicitud->ID_SOLICITUD }}</h2>
            <p class="text-sm text-base-content/50">Registrada correctamente el {{ $solicitud->FECHA_SOLICITUD?->format('d/m/Y H:i') }}</p>
        </div>

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
                        <dt class="text-xs text-base-content/50">Sesiones simultáneas</dt>
                        <dd class="font-medium">{{ ucfirst($solicitud->SESIONES_SIMULTANEAS) }}</dd>
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
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-base-content/50">Dirección de envío</dt>
                        <dd class="font-medium">{{ $solicitud->DIRECCION_ENVIO ?: '—' }}</dd>
                    </div>
                    @if($solicitud->HORARIO_DE_ATENCION)
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-base-content/50">Horario de atención</dt>
                            <dd class="font-medium">{{ $solicitud->HORARIO_DE_ATENCION }}</dd>
                        </div>
                    @endif
                    @if($solicitud->OBSERVACIONES)
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-base-content/50">Observaciones</dt>
                            <dd class="font-medium">{{ $solicitud->OBSERVACIONES }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card bg-base-100 shadow mb-4">
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

        <div class="alert alert-success text-sm">
            <span>Gracias. Tu solicitud ha sido enviada y nuestro equipo dará seguimiento a la brevedad. Puedes cerrar esta ventana.</span>
        </div>
    </div>
</body>
</html>