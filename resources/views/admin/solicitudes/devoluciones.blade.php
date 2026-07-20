<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Devoluciones</h2>
    </x-slot>

    <x-alert />

    {{-- FILTROS --}}
    <form method="GET" class="card bg-base-100 shadow mb-4">
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="form-control flex-1 min-w-[200px]">
                    <label class="label py-0"><span class="label-text text-xs">Buscar</span></label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                        placeholder="Empresa o sede..."
                        class="input input-bordered input-sm w-full" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-funnel class="w-4 h-4" />
                        Filtrar
                    </button>
                    <a href="{{ route('admin.devoluciones.index') }}" class="btn btn-ghost btn-sm gap-1">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                        Limpiar
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card md:bg-base-100 md:shadow">
        <div class="card-body p-0">

            {{-- VISTA DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Empresa / Sede</th>
                            <th>Responsable</th>
                            <th>Fecha solicitud</th>
                            <th class="text-center">Artículos</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            @php
                                $esEnviada = $solicitud->isEnviada();
                            @endphp
                            <tr>
                                <td class="font-mono text-sm">{{ $solicitud->ID_SOLICITUD }}</td>
                                <td>
                                    <p class="font-medium">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                    <p class="text-sm text-base-content/60">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                </td>
                                <td>
                                    <p>{{ $solicitud->RESPONSABLE_NOMBRE }}</p>
                                    <p class="text-sm text-base-content/60">{{ $solicitud->RESPONSABLE_CORREO }}</p>
                                </td>
                                <td class="text-sm">{{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}</td>
                                <td class="text-center font-mono">{{ $solicitud->articulos_count }}</td>
                                <td>
                                    <span class="badge {{ $esEnviada ? 'badge-info' : 'badge-success' }}">
                                        {{ $esEnviada ? 'Por retornar' : 'En devolución' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        @if($esEnviada)
                                            <form method="POST" action="{{ route('admin.solicitudes.estado', $solicitud->ID_SOLICITUD) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="retornada">
                                                <button type="submit" class="btn btn-info btn-xs gap-1" title="Marcar como retornada">
                                                    <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                                                    Marcar retornada
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.solicitudes.edit', $solicitud->ID_SOLICITUD) }}"
                                                class="btn btn-success btn-xs gap-1" title="Procesar devolución">
                                                <x-heroicon-o-inbox-arrow-down class="w-4 h-4" />
                                                Procesar
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                            class="btn btn-outline btn-xs" title="Ver detalle">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-base-content/50 py-8">
                                    No hay solicitudes pendientes de devolución.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL: cards --}}
            <div class="md:hidden">
                @forelse($solicitudes as $solicitud)
                    @php
                        $esEnviada = $solicitud->isEnviada();
                        $tabClasses = $esEnviada ? 'bg-info/20 text-info-content border-info/40' : 'bg-success/20 text-success-content border-success/40';
                        $cardBorder = $esEnviada ? 'border-info/40' : 'border-success/40';
                    @endphp
                    <div class="px-3 pt-6 pb-3">
                        <div class="relative">
                            <div class="absolute -top-6 left-0 h-6 px-3 flex items-center gap-1.5
                                        rounded-t-lg border border-b-0 text-xs font-medium {{ $tabClasses }}">
                                <x-heroicon-o-arrow-uturn-left class="w-3.5 h-3.5" />
                                Solicitud #{{ $solicitud->ID_SOLICITUD }}
                            </div>

                            <div class="bg-base-100 rounded-b-xl rounded-tr-xl border {{ $cardBorder }} p-3">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm truncate">{{ $solicitud->empresa?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-base-content/50 truncate">{{ $solicitud->sede?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-base-content/40 truncate">{{ $solicitud->RESPONSABLE_NOMBRE }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs text-base-content/50">
                                            {{ \Carbon\Carbon::parse($solicitud->FECHA_SOLICITUD)->format('d/m/Y') }}
                                        </p>
                                        <p class="text-xs font-mono text-base-content/40 mt-0.5">
                                            {{ $solicitud->articulos_count }} art.
                                        </p>
                                        <span class="badge badge-xs {{ $esEnviada ? 'badge-info' : 'badge-success' }} mt-1">
                                            {{ $esEnviada ? 'Por retornar' : 'En devolución' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    @if($esEnviada)
                                        <form method="POST" action="{{ route('admin.solicitudes.estado', $solicitud->ID_SOLICITUD) }}" class="flex-1">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="retornada">
                                            <button type="submit" class="btn btn-info btn-sm gap-1 w-full">
                                                <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                                                Marcar retornada
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.solicitudes.edit', $solicitud->ID_SOLICITUD) }}"
                                            class="btn btn-success btn-sm gap-1 flex-1">
                                            <x-heroicon-o-inbox-arrow-down class="w-4 h-4" />
                                            Procesar
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                        class="btn btn-outline btn-sm">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay solicitudes pendientes de devolución.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $solicitudes->links() }}</div>

</x-app-layout>