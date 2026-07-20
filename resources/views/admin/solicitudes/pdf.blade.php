<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }

        .page { padding: 30px 35px; }
        .page-break { page-break-after: always; }

        /* Encabezado */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 2px solid #0F448A; padding-bottom: 12px; }
        .header h1 { font-size: 18px; color: #0F448A; }
        .header .meta { text-align: right; font-size: 10px; color: #555; }
        .header .meta strong { font-size: 13px; color: #1a1a1a; display: block; }

        /* Secciones */
        .section { margin-bottom: 16px; }
        .section-title { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #0F448A; font-weight: bold; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }

        /* Grid de datos */
        .grid-2 { width: 100%; border-collapse: collapse; }
        .grid-2 td { width: 50%; vertical-align: top; padding: 3px 6px 3px 0; }
        .label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.3px; }
        .value { font-size: 11px; color: #1a1a1a; font-weight: 500; }

        /* Resumen de artículos página 1 */
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .summary-table th { background: #0F448A; color: white; font-size: 9px; padding: 5px 8px; text-align: left; }
        .summary-table td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
        .summary-table tr:nth-child(even) td { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Tabla artículos página 2 */
        .exam-title { font-size: 12px; font-weight: bold; color: #0F448A; margin: 12px 0 6px 0; }
        .exam-meta { font-size: 10px; color: #555; margin-bottom: 6px; }
        .articles-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .articles-table th { background: #6366f1; color: white; font-size: 9px; padding: 4px 6px; text-align: left; }
        .articles-table td { padding: 4px 6px; border-bottom: 1px solid #eeeeee; font-size: 9px; }
        .articles-table tr:nth-child(even) td { background: #fafafa; }

        /* Badge */
        .badge { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 9px; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-success { background: #d1fae5; color: #065f46; }

        .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 9px; color: #aaa; text-align: center; }
    </style>
</head>
<body>

{{-- ============ PÁGINA 1 ============ --}}
<div class="page page-break">

    <div class="header">
        <div>
            <h1>Solicitud de Material</h1>
            <div style="font-size:10px; color:#555; margin-top:4px;">
                Generado el {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
        <div class="meta">
            <strong>#{{ $solicitud->ID_SOLICITUD }}</strong>
            <span class="badge {{ $solicitud->ESTADO_SOLICITUD === 'pendiente' ? 'badge-warning' : ($solicitud->ESTADO_SOLICITUD === 'enviada' ? 'badge-info' : 'badge-success') }}">
                {{ ucfirst($solicitud->ESTADO_SOLICITUD) }}
            </span>
        </div>
    </div>

    {{-- Empresa / Sede --}}
    <div class="section">
        <div class="section-title">Empresa y sede</div>
        <table class="grid-2">
            <tr>
                <td><span class="label">Empresa</span><br><span class="value">{{ $solicitud->empresa?->nombre ?? '—' }}</span></td>
                <td><span class="label">Sede</span><br><span class="value">{{ $solicitud->sede?->nombre ?? '—' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Dirección de envío</span><br><span class="value">{{ $solicitud->DIRECCION_ENVIO ?: '—' }}</span></td>
                <td><span class="label">Sesiones simultáneas</span><br><span class="value">{{ ucfirst($solicitud->SESIONES_SIMULTANEAS) }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Horario de atención</span><br><span class="value">{{ $solicitud->HORARIO_DE_ATENCION ?: '—' }}</span></td>
                <td><span class="label">Fecha solicitud</span><br><span class="value">{{ $solicitud->FECHA_SOLICITUD?->format('d/m/Y') ?? '—' }}</span></td>
            </tr>
        </table>
    </div>

    {{-- Contacto --}}
    <div class="section">
        <div class="section-title">Responsable y contacto</div>
        <table class="grid-2">
            <tr>
                <td><span class="label">Responsable</span><br><span class="value">{{ $solicitud->RESPONSABLE_NOMBRE }}</span></td>
                <td><span class="label">Contacto asignado</span><br><span class="value">{{ $solicitud->contacto?->nombre ?? '—' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Correo</span><br><span class="value">{{ $solicitud->RESPONSABLE_CORREO ?: '—' }}</span></td>
                <td><span class="label">Teléfono / Celular</span><br><span class="value">{{ $solicitud->RESPONSABLE_TELEFONO ?: '—' }} / {{ $solicitud->RESPONSABLE_CELULAR ?: '—' }}</span></td>
            </tr>
        </table>
    </div>

    @if($solicitud->OBSERVACIONES)
    <div class="section">
        <div class="section-title">Observaciones</div>
        <p style="font-size:10px; color:#444;">{{ $solicitud->OBSERVACIONES }}</p>
    </div>
    @endif

    {{-- Resumen de exámenes --}}
    <div class="section">
        <div class="section-title">Resumen de exámenes y material</div>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Examen</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Candidatos</th>
                    <th class="text-center">Artículos</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitud->examenes as $examen)
                <tr>
                    <td>{{ $examen->EXAMEN }}</td>
                    <td class="text-center">{{ $examen->FECHA?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-center">{{ $examen->CANTIDAD }}</td>
                    <td class="text-center">{{ $examen->articulos->count() }}</td>
                    <td>
                        <span class="badge {{ $examen->ESTADO === 'pendiente' ? 'badge-warning' : ($examen->ESTADO === 'enviado' ? 'badge-info' : 'badge-success') }}">
                            {{ ucfirst($examen->ESTADO) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Solicitud #{{ $solicitud->ID_SOLICITUD }} — {{ config('app.name') }} — Página 1 de 2
    </div>
</div>

{{-- ============ PÁGINA 2 ============ --}}
<div class="page">

    <div class="header">
        <div>
            <h1>Detalle de material por examen</h1>
            <div style="font-size:10px; color:#555; margin-top:4px;">Solicitud #{{ $solicitud->ID_SOLICITUD }} — {{ $solicitud->empresa?->nombre }}</div>
        </div>
        <div class="meta">
            <strong>{{ now()->format('d/m/Y') }}</strong>
        </div>
    </div>

    @foreach($solicitud->examenes as $examen)
        <div class="exam-title">{{ $examen->EXAMEN }}</div>
        <div class="exam-meta">
            Fecha: {{ $examen->FECHA?->format('d/m/Y') ?? '—' }} &nbsp;|&nbsp;
            Candidatos: {{ $examen->CANTIDAD }} &nbsp;|&nbsp;
            Estado: {{ ucfirst($examen->ESTADO) }}
        </div>

        @if($examen->articulos->isNotEmpty())
        <table class="articles-table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Serie</th>
                    <th>Nombre</th>
                    <th>Formato</th>
                    <th class="text-center">Enviados</th>
                    <th class="text-center">Retornados</th>
                    <th class="text-center">Destrucción</th>
                    <th class="text-center">Perdidos</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examen->articulos as $sa)
                <tr>
                    <td>{{ $sa->FOLIO }}</td>
                    <td>{{ $sa->SERIE ?: '—' }}</td>
                    <td>{{ $sa->NOMBRE }}</td>
                    <td>{{ $sa->FORMATO ?: '—' }}</td>
                    <td class="text-center">{{ $sa->CANTIDAD_ENVIADA }}</td>
                    <td class="text-center">{{ $sa->CANTIDAD_A_ALMACEN }}</td>
                    <td class="text-center">{{ $sa->CANTIDAD_A_DESTRUCCION }}</td>
                    <td class="text-center">{{ $sa->CANTIDAD_PERDIDOS }}</td>
                    <td>
                        <span class="badge {{ $sa->ESTADO === 'retornado' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($sa->ESTADO) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p style="font-size:10px; color:#aaa; margin-bottom:10px;">Sin artículos registrados.</p>
        @endif
    @endforeach

    <div class="footer">
        Solicitud #{{ $solicitud->ID_SOLICITUD }} — {{ config('app.name') }} — Página 2 de 2
    </div>
</div>

</body>
</html>