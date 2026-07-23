<!DOCTYPE html>
<html lang="es" data-theme="rq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exámenes — Solicitud #{{ $solicitud->ID_SOLICITUD }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen p-4">
    <div class="max-w-2xl mx-auto py-6">
        <h2 class="text-xl font-semibold mb-1">Solicitud #{{ $solicitud->ID_SOLICITUD }}</h2>
        <p class="text-sm text-base-content/50 mb-6">{{ $solicitud->empresa?->nombre }} — {{ $solicitud->sede?->nombre }}</p>

        @if(session('success'))
            <div class="alert alert-success text-sm mb-4"><span>{{ session('success') }}</span></div>
        @endif
        @if($errors->any())
            <div class="alert alert-error text-sm mb-4"><span>{{ $errors->first() }}</span></div>
        @endif

        <div class="card bg-base-100 shadow mb-4">
            <div class="card-body">
                <h3 class="card-title text-base mb-3">Exámenes agregados</h3>
                @forelse($solicitud->examenes as $examen)
                    <div class="flex items-center justify-between py-2 border-b border-base-300 last:border-0">
                        <div>
                            <p class="font-medium text-sm">{{ $examen->EXAMEN }}</p>
                            <p class="text-xs text-base-content/50">{{ $examen->CANTIDAD }} candidatos — {{ $examen->FECHA?->format('d/m/Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('portal.solicitudes.examenes.destroy', [$solicitud, $examen]) }}"
                            onsubmit="return confirm('¿Eliminar este examen?')">
                            @csrf @method('DELETE')
                            <input type="hidden" name="pin" value="{{ $pin }}" />
                            <input type="hidden" name="contacto" value="{{ $contactoId }}" />
                            <button type="submit" class="btn btn-ghost btn-xs text-error">Eliminar</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50 text-center py-4">Aún no has agregado exámenes.</p>
                @endforelse
            </div>
        </div>

        <div class="card bg-base-100 shadow mb-4">
            <div class="card-body">
                <h3 class="card-title text-base mb-3">Agregar examen</h3>
                <form method="POST" action="{{ route('portal.solicitudes.examenes.store', $solicitud) }}">
                    @csrf
                    <input type="hidden" name="pin" value="{{ $pin }}" />
                    <input type="hidden" name="contacto" value="{{ $contactoId }}" />

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div class="form-control sm:col-span-3">
                            <label class="label"><span class="label-text">Tipo de examen *</span></label>
                            <select name="tipo_examen_id" class="select select-bordered" required>
                                <option value="">Selecciona…</option>
                                @foreach($tipoExamenes as $te)
                                    <option value="{{ $te->id }}">{{ $te->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Cantidad de candidatos *</span></label>
                            <input type="number" name="cantidad" min="1" class="input input-bordered" required />
                        </div>
                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Fecha *</span></label>
                            <input type="date" name="fecha" class="input input-bordered" required />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Agregar examen</button>
                </form>
            </div>
        </div>

        <div class="alert alert-info text-sm mb-4">
            <span>Tu solicitud fue registrada. Puedes seguir agregando exámenes o terminar para ver el resumen.</span>
        </div>

        <a href="{{ route('portal.solicitudes.resumen', ['solicitud' => $solicitud->ID_SOLICITUD, 'pin' => $pin, 'contacto' => $contactoId]) }}"
            class="btn btn-success w-full gap-1">
            <x-heroicon-o-check-circle class="w-5 h-5" />
            Terminar y ver resumen
        </a>
    </div>
</body>
</html>