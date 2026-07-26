<!DOCTYPE html>
<html lang="es" data-theme="rq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de Solicitudes — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen p-4">
    <div class="max-w-2xl mx-auto py-6">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-xl font-semibold">Historial de solicitudes</h2>
            <form method="POST" action="{{ route('portal.salir') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-xs">Salir</button>
            </form>
        </div>
        <p class="text-sm text-base-content/50 mb-6">{{ $contacto->nombre }} {{ $contacto->apellidos }} — {{ $contacto->empresa?->nombre }}</p>

        @if(session('success'))
            <div class="alert alert-success text-sm mb-4"><span>{{ session('success') }}</span></div>
        @endif

        <a href="{{ route('portal.solicitudes.create') }}" class="btn btn-primary w-full gap-1 mb-4">
            <x-heroicon-o-plus class="w-5 h-5" />
            Nueva solicitud
        </a>

        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="divide-y divide-base-300">
                    @forelse($solicitudes as $solicitud)
                        <a href="{{ route('portal.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                            class="flex items-center justify-between px-4 py-3 hover:bg-base-200 transition-colors">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-sm">#{{ $solicitud->ID_SOLICITUD }}</span>
                                    <span class="text-base-content/50 text-sm truncate">{{ $solicitud->sede?->nombre }}</span>
                                </div>
                                <p class="text-xs text-base-content/40 mt-0.5">
                                    {{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}
                                    · {{ $solicitud->examenes_count }} {{ $solicitud->examenes_count === 1 ? 'examen' : 'exámenes' }}
                                </p>
                            </div>
                            @php
                                $badge = match($solicitud->ESTADO_SOLICITUD) {
                                    'pendiente' => 'badge-warning',
                                    'enviada'   => 'badge-info',
                                    'retornada' => 'badge-success',
                                    default     => 'badge-ghost',
                                };
                            @endphp
                            <span class="badge badge-sm {{ $badge }} shrink-0 ml-2">
                                {{ ucfirst($solicitud->ESTADO_SOLICITUD) }}
                            </span>
                        </a>
                    @empty
                        <div class="text-center text-base-content/50 py-8 text-sm">
                            Aún no has creado ninguna solicitud.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @if($solicitudes->hasPages())
            <div class="mt-4">{{ $solicitudes->links() }}</div>
        @endif
    </div>
</body>
</html>