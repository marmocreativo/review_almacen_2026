<!DOCTYPE html>
<html lang="es" data-theme="rq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal de Solicitudes — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen flex items-center justify-center p-4">
    <div class="card bg-base-100 shadow-xl w-full max-w-sm">
        <div class="card-body">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-12" />
            </div>

            <h2 class="text-xl font-semibold mb-1 text-center">Portal de Solicitudes</h2>
            <p class="text-sm text-base-content/60 text-center mb-4">
                Desde aquí puedes crear nuevas solicitudes de material y consultar el historial y estado de tus
                solicitudes anteriores.
            </p>

            @if($errors->any())
                <div class="alert alert-error text-sm mb-4">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('portal.verificar') }}">
                @csrf
                <div class="form-control mb-4">
                    <label class="label py-1"><span class="label-text text-xs">PIN de acceso</span></label>
                    <input type="text" name="pin" maxlength="8" autofocus required
                        placeholder="PIN de 8 caracteres"
                        class="input input-bordered w-full text-center font-mono text-lg tracking-widest uppercase" />
                </div>
                <button type="submit" class="btn btn-primary w-full">Ingresar</button>
            </form>

            <div class="divider text-xs text-base-content/40">¿No tienes PIN?</div>
            <p class="text-xs text-base-content/50 text-center">
                Si aún no cuentas con un PIN de acceso, comunícate con nosotros para que te lo asignemos.
            </p>
        </div>
    </div>
</body>
</html>