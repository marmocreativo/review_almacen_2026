<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #1a1a1a; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 480px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #0F448A; color: white; padding: 24px 28px; }
        .header h1 { font-size: 18px; margin: 0; }
        .body { padding: 28px; }
        .pin-box { background: #F2F2F2; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
        .pin-box .pin { font-size: 28px; font-weight: bold; letter-spacing: 4px; color: #0F448A; font-family: monospace; }
        .footer { background: #f9f9f9; padding: 16px 28px; font-size: 11px; color: #aaa; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Tu PIN de acceso</h1>
    </div>
    <div class="body">
        <p>Hola {{ $contacto->nombre }},</p>
        <p>Este es tu PIN de acceso para crear solicitudes de material desde el portal de <strong>{{ $empresa->nombre }}</strong>:</p>
        <div class="pin-box">
            <span class="pin">{{ $contacto->pin }}</span>
        </div>
        <p>Guarda este PIN en un lugar seguro. No lo compartas con nadie.</p>
        <p style="font-size:12px; color:#888;">Si no solicitaste este acceso, ignora este correo.</p>
    </div>
    <div class="footer">
        Este correo fue generado automáticamente por {{ config('app.name') }}. Por favor no responda directamente.
    </div>
</div>
</body>
</html>