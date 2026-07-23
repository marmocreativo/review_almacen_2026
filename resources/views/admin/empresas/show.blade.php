<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('admin.empresas.index') }}" class="btn btn-ghost btn-sm btn-square shrink-0">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <div class="avatar shrink-0">
                    <div class="mask mask-squircle w-8 h-8">
                        <img src="{{ asset('storage/' . $empresa->logo) }}"
                            onerror="this.src='https://placehold.co/32x32?text=E'"
                            alt="{{ $empresa->nombre }}" />
                    </div>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold truncate">{{ $empresa->nombre }}</h2>
                    <p class="text-xs text-base-content/50 truncate hidden sm:block">{{ $empresa->razon_social ?? '' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="badge {{ $empresa->estado === 'activo' ? 'badge-success' : 'badge-error' }}">
                    {{ $empresa->estado }}
                </span>
                <a href="{{ route('admin.empresas.edit', $empresa) }}" class="btn btn-info btn-sm gap-1">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    <span class="hidden sm:inline">Editar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <x-alert />

    <div class="space-y-4">

        {{-- Datos generales --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title text-base mb-1">Datos generales</h3>
                <dl class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-base-content/50">RFC</dt>
                        <dd class="font-mono font-medium">{{ $empresa->rfc ?: '—' }}</dd>
                    </div>
                    <div class="col-span-2 md:col-span-2">
                        <dt class="text-xs text-base-content/50">Razón social</dt>
                        <dd class="font-medium">{{ $empresa->razon_social ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/50">Sedes</dt>
                        <dd class="font-medium">{{ $empresa->sedes->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Sedes --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="card-title text-base">Sedes</h3>
                    <a href="{{ route('admin.empresas.sedes.create', $empresa) }}"
                        class="btn btn-ghost btn-xs gap-1">
                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                        Nueva
                    </a>
                </div>

                @forelse($empresa->sedes as $sede)
                    <div class="collapse collapse-arrow bg-base-200 mb-2 rounded-lg">
                        <input type="checkbox" />
                        <div class="collapse-title text-sm font-medium py-3 min-h-0">
                            <div class="flex items-center justify-between pr-4">
                                <span>{{ $sede->nombre }}</span>
                                <span class="badge badge-sm {{ $sede->estado === 'activo' ? 'badge-success' : 'badge-ghost' }}">
                                    {{ $sede->estado }}
                                </span>
                            </div>
                        </div>
                        <div class="collapse-content text-sm">
                            @if($sede->calle_y_numero || $sede->colonia_barrio)
                                <p class="text-base-content/70 mb-1">
                                    {{ implode(', ', array_filter([
                                        $sede->calle_y_numero,
                                        $sede->colonia_barrio,
                                        $sede->alcaldia_municipio,
                                        $sede->ciudad,
                                        $sede->estado_republica,
                                        $sede->codigo_postal,
                                    ])) }}
                                </p>
                            @endif
                            @if($sede->referencias)
                                <p class="text-base-content/50 text-xs mb-2">{{ $sede->referencias }}</p>
                            @endif

                            @if($sede->contactos->count())
                                <div class="mt-2 space-y-1">
                                    <p class="text-xs text-base-content/40 uppercase tracking-wide">Contactos</p>
                                    @foreach($sede->contactos as $contacto)
                                        <div class="text-xs">
                                            <span class="font-medium">{{ $contacto->nombre }} {{ $contacto->apellidos }}</span>
                                            @if($contacto->telefono)
                                                <span class="text-base-content/50 ml-1">· {{ $contacto->telefono }}</span>
                                            @endif
                                            @if($contacto->correo)
                                                <br>
                                                <a href="mailto:{{ $contacto->correo }}" class="text-primary">{{ $contacto->correo }}</a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-base-content/40 mt-2">Sin contactos asignados.</p>
                            @endif

                            <div class="flex gap-2 mt-3">
                                <a href="{{ route('admin.empresas.sedes.edit', [$empresa, $sede]) }}"
                                    class="btn btn-ghost btn-xs">Editar sede</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50 text-center py-4">No hay sedes registradas.</p>
                @endforelse
            </div>
        </div>

        {{-- Contactos del cliente --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="card-title text-base">Contactos</h3>
                    <a href="{{ route('admin.empresas.contactos.index', $empresa) }}"
                        class="btn btn-ghost btn-xs gap-1">
                        <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                        Ver todos
                    </a>
                </div>

                @forelse($empresa->contactos()->with('sedes')->latest()->take(5)->get() as $contacto)
                    <div class="flex items-center justify-between py-2 border-b border-base-300 last:border-0 text-sm">
                        <div class="min-w-0">
                            <p class="font-medium truncate">{{ $contacto->nombre }} {{ $contacto->apellidos }}</p>
                            <p class="text-xs text-base-content/50 truncate">
                                {{ $contacto->correo ?? '—' }} @if($contacto->telefono) · {{ $contacto->telefono }} @endif
                            </p>
                            @if($contacto->sedes->count())
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($contacto->sedes as $sedeContacto)
                                        <span class="badge badge-ghost badge-xs">{{ $sedeContacto->nombre }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('admin.empresas.contactos.edit', [$empresa, $contacto]) }}"
                            class="btn btn-ghost btn-xs btn-square shrink-0">
                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                        </a>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50 text-center py-4">No hay contactos registrados.</p>
                @endforelse
            </div>
        </div>

        {{-- Solicitudes --}}
        <div class="card md:bg-base-100 md:shadow">
            <div class="card-body p-0">
                <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                    <h3 class="font-semibold text-base">Solicitudes</h3>
                    <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-ghost btn-xs gap-1">
                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                        Nueva
                    </a>
                </div>

                <div class="divide-y divide-base-300">
                    @forelse($solicitudes as $solicitud)
                        <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
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
                                {{ $solicitud->ESTADO_SOLICITUD }}
                            </span>
                        </a>
                    @empty
                        <div class="text-center text-base-content/50 py-8 text-sm">
                            No hay solicitudes para esta empresa.
                        </div>
                    @endforelse
                </div>

                @if($solicitudes->hasPages())
                    <div class="px-4 py-3">{{ $solicitudes->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>