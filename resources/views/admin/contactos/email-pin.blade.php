<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu PIN de acceso</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.1);">

                    {{-- Header con logo --}}
                    <tr>
                        <td align="center" style="background-color:#0F448A; padding:28px 24px;">
                            <img src="{{ asset('images/logo_letras_blanco.png') }}" alt="{{ config('app.name') }}" height="40" style="display:block;">
                        </td>
                    </tr>

                    {{-- Contenido --}}
                    <tr>
                        <td style="padding:32px 32px 8px 32px;">
                            <p style="margin:0 0 16px 0; font-size:15px; line-height:1.5; color:#334155;">
                                Hola <strong>{{ $contacto->nombre }}</strong>,
                            </p>
                            <p style="margin:0 0 24px 0; font-size:15px; line-height:1.6; color:#334155;">
                                Este es tu PIN de acceso al <strong>Portal de Solicitudes</strong> de {{ config('app.name') }},
                                a nombre de <strong>{{ $empresa->nombre }}</strong>. Con él podrás crear nuevas solicitudes
                                de material y consultar el historial y estado de tus solicitudes anteriores.
                            </p>
                        </td>
                    </tr>

                    {{-- PIN destacado --}}
                    <tr>
                        <td align="center" style="padding:0 32px 24px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; border:1px dashed #94a3b8; border-radius:10px; width:100%;">
                                <tr>
                                    <td align="center" style="padding:18px 16px;">
                                        <p style="margin:0 0 6px 0; font-size:11px; letter-spacing:1px; text-transform:uppercase; color:#64748b;">
                                            Tu PIN de acceso
                                        </p>
                                        <p style="margin:0; font-size:28px; font-weight:bold; letter-spacing:6px; color:#0D3A75; font-family: 'Courier New', monospace;">
                                            {{ $contacto->pin }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Botón de acceso --}}
                    <tr>
                        <td align="center" style="padding:0 32px 32px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="border-radius:8px; background-color:#0F448A;">
                                        <a href="{{ route('portal.login') }}"
                                            target="_blank"
                                            style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:8px;">
                                            Ingresar al Portal
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Instrucciones --}}
                    <tr>
                        <td style="padding:0 32px 8px 32px;">
                            <p style="margin:0 0 8px 0; font-size:13px; font-weight:600; color:#334155;">
                                ¿Cómo lo uso?
                            </p>
                            <ol style="margin:0 0 8px 0; padding-left:18px; font-size:13px; line-height:1.7; color:#64748b;">
                                <li>Ingresa al portal desde el botón de arriba, o visita:<br>
                                    <a href="{{ route('portal.login') }}" style="color:#0F448A; word-break:break-all;">{{ route('portal.login') }}</a>
                                </li>
                                <li>Escribe tu PIN de 8 caracteres cuando se te solicite.</li>
                                <li>Desde ahí podrás crear una nueva solicitud o consultar el estado de las anteriores.</li>
                            </ol>
                        </td>
                    </tr>

                    {{-- Nota de seguridad --}}
                    <tr>
                        <td style="padding:16px 32px 32px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fffbeb; border-left:3px solid #f59e0b; border-radius:6px;">
                                <tr>
                                    <td style="padding:12px 14px;">
                                        <p style="margin:0; font-size:12.5px; line-height:1.5; color:#78350f;">
                                            Este PIN es personal e intransferible. Si tú no solicitaste este correo o
                                            crees que alguien más tiene acceso a tu PIN, contáctanos de inmediato para
                                            generar uno nuevo.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="background-color:#f8fafc; padding:20px 24px; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:11.5px; color:#94a3b8;">
                                {{ config('app.name') }} &middot; Este es un correo automático, por favor no respondas directamente.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>