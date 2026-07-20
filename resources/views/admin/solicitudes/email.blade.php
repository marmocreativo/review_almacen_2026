<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #1a1a1a; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #0F448A; color: white; padding: 24px 28px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header p { margin: 0; font-size: 12px; opacity: .8; }
        .body { padding: 24px 28px; }
        .section { margin-bottom: 20px; }
        .section h2 { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #0F448A; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 10px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .label { color: #888; font-size: 11px; }
        .value { font-size: 12px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #0F448A; color: white; padding: 6px 8px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; }
        tr:nth-child(even) td { background: #f9f9f9; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .footer { background: #f9f9f9; padding: 16px 28px; font-size: 11px; color: #aaa; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ public_path('logo.png') }}" style="height:40px; margin-bottom:12px; display:block;">
        <h1>Solicitud #{{ $solicitud->ID_SOLICITUD }}</h1>
        <p>{{ config('app.name') }} — {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="body">

        <div class="section">
            <h2>Empresa y sede</h2>
            <div class="row">
                <span class="label">Empresa</span>
                <span class="value">{{ $solicitud->empresa?->nombre ?? '—' }}</span>
            </div>
            <div class="row">
                <span class="label">Sede</span>
                <span class="value">{{ $solicitud->sede?->nombre ?? '—' }}</span>
            </div>
            <div class="row">
                <span class="label">Dirección de envío</span>
                <span class="value">{{ $solicitud->DIRECCION_ENVIO ?: '—' }}</span>
            </div>
            <div class="row">
                <span class="label">Estado</span>
                <span class="badge {{ $solicitud->ESTADO_SOLICITUD === 'pendiente' ? 'badge-warning' : ($solicitud->ESTADO_SOLICITUD === 'enviada' ? 'badge-info' : 'badge-success') }}">
                    {{ ucfirst($solicitud->ESTADO_SOLICITUD) }}
                </span>
            </div>
        </div>

        <div class="section">
            <h2>Responsable</h2>
            <div class="row">
                <span class="label">Nombre</span>
                <span class="value">{{ $solicitud->RESPONSABLE_NOMBRE }}</span>
            </div>
            <div class="row">
                <span class="label">Correo</span>
                <span class="value">{{ $solicitud->RESPONSABLE_CORREO ?: '—' }}</span>
            </div>
            <div class="row">
                <span class="label">Teléfono</span>
                <span class="value">{{ $solicitud->RESPONSABLE_TELEFONO ?: '—' }}</span>
            </div>
        </div>

        <div class="section">
            <h2>Resumen de exámenes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Examen</th>
                        <th>Fecha</th>
                        <th>Candidatos</th>
                        <th>Artículos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitud->examenes as $examen)
                    <tr>
                        <td>{{ $examen->EXAMEN }}</td>
                        <td>{{ $examen->FECHA?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $examen->CANTIDAD }}</td>
                        <td>{{ $examen->articulos->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($solicitud->OBSERVACIONES)
        <div class="section">
            <h2>Observaciones</h2>
            <p style="font-size:12px; color:#444;">{{ $solicitud->OBSERVACIONES }}</p>
        </div>
        @endif

        <p style="font-size:12px; color:#555;">Se adjunta el detalle completo en PDF.</p>
    </div>
    <div class="footer">
        Este correo fue generado automáticamente por {{ config('app.name') }}. Por favor no responda directamente.
    </div>
</div>
</body>
</html>