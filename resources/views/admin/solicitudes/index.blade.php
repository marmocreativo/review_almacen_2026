<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Solicitudes</h2>
            <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-primary btn-sm gap-1">
                <x-heroicon-o-plus class="w-4 h-4" />
                <span class="hidden sm:inline">Nueva solicitud</span>
                <span class="sm:hidden">Nueva</span>
            </a>
        </div>
    </x-slot>

    <x-alert />

    {{-- FILTROS --}}
    <form method="GET" class="card bg-base-100 shadow mb-4">
        <div class="card-body py-3">
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-3 items-end">
                <div class="form-control col-span-2 sm:col-span-1">
                    <label class="label py-0"><span class="label-text text-xs">Buscar</span></label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                        placeholder="Empresa o sede..."
                        class="input input-bordered input-sm w-full sm:w-48" />
                </div>
                <div class="form-control col-span-1">
                    <label class="label py-0"><span class="label-text text-xs">Estado</span></label>
                    <select name="estado" class="select select-bordered select-sm w-full">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="enviada"   {{ request('estado') === 'enviada'   ? 'selected' : '' }}>Enviada</option>
                        <option value="retornada" {{ request('estado') === 'retornada' ? 'selected' : '' }}>Retornada</option>
                    </select>
                </div>
                <div class="col-span-1 flex gap-2 items-end">
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-funnel class="w-4 h-4" />
                        Filtrar
                    </button>
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-ghost btn-sm gap-1">
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
                            <th>Fecha</th>
                            <th class="text-center">Exámenes</th>
                            <th class="text-center">Artículos</th>
                            <th>Estado</th>
                            <th>Factura</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
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
                                <td class="text-center font-mono">{{ $solicitud->examenes_count }}</td>
                                <td class="text-center font-mono">{{ $solicitud->articulos_count }}</td>
                                <td>
                                    @php
                                        $badgeEstado = match($solicitud->ESTADO_SOLICITUD) {
                                            'pendiente' => 'badge-warning',
                                            'enviada'   => 'badge-info',
                                            'retornada' => 'badge-success',
                                            default     => 'badge-ghost',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeEstado }}">{{ ucfirst($solicitud->ESTADO_SOLICITUD) }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeFactura = $solicitud->ESTADO_FACTURA === 'facturada' ? 'badge-success' : 'badge-ghost';
                                    @endphp
                                    <span class="badge {{ $badgeFactura }}">{{ ucfirst($solicitud->ESTADO_FACTURA) }}</span>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                            class="btn btn-outline btn-xs gap-1" title="Ver detalle">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                            Ver
                                        </a>
                                        @if($solicitud->isPendiente() || $solicitud->isEnviada())
                                            <a href="{{ route('admin.solicitudes.edit', $solicitud->ID_SOLICITUD) }}"
                                                class="btn btn-info btn-xs gap-1"
                                                title="{{ $solicitud->isPendiente() ? 'Editar' : 'Retornar artículos' }}">
                                                @if($solicitud->isPendiente())
                                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                                    Editar
                                                @else
                                                    <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                                                    Retornar
                                                @endif
                                            </a>
                                        @endif
                                        @if($solicitud->isPendiente())
                                            <form method="POST"
                                                action="{{ route('admin.solicitudes.destroy', $solicitud->ID_SOLICITUD) }}"
                                                onsubmit="return confirm('¿Eliminar esta solicitud? Se revertirán todas las cantidades.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-error btn-xs" title="Eliminar">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-base-content/50 py-8">
                                    No hay solicitudes registradas.
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
                        $estado = $solicitud->ESTADO_SOLICITUD;
                        $tabClasses = match($estado) {
                            'pendiente' => 'bg-warning/20 text-warning-content border-warning/40',
                            'enviada'   => 'bg-info/20 text-info-content border-info/40',
                            'retornada' => 'bg-success/20 text-success-content border-success/40',
                            default     => 'bg-base-200 text-base-content border-base-300',
                        };
                        $cardBorder = match($estado) {
                            'pendiente' => 'border-warning/40',
                            'enviada'   => 'border-info/40',
                            'retornada' => 'border-success/40',
                            default     => 'border-base-300',
                        };
                    @endphp

                    <div class="px-3 pt-6 pb-3">
                        <div class="relative">

                            {{-- Pestaña folder --}}
                            <div class="absolute -top-6 left-0 h-6 px-3 flex items-center gap-1.5
                                        rounded-t-lg border border-b-0 text-xs font-medium {{ $tabClasses }}">
                                <x-heroicon-o-folder class="w-3.5 h-3.5" />
                                Solicitud #{{ $solicitud->ID_SOLICITUD }}
                            </div>

                            {{-- Cuerpo de la card --}}
                            <div class="bg-base-100 rounded-b-xl rounded-tr-xl border {{ $cardBorder }} p-3">

                                {{-- Fila superior: empresa + fecha --}}
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
                                            {{ $solicitud->examenes_count }} exám.
                                        </p>
                                        <p class="text-xs font-mono text-base-content/40">
                                            {{ $solicitud->articulos_count }} art.
                                        </p>
                                    </div>
                                </div>

                                {{-- Línea de progreso --}}
                                <div class="flex items-center mb-4">
                                    {{-- Paso 1: Pendiente --}}
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center
                                            {{ in_array($estado, ['pendiente','enviada','retornada'])
                                                ? 'bg-primary border-primary'
                                                : 'border-base-300 bg-base-100' }}">
                                            @if(in_array($estado, ['enviada','retornada']))
                                                <x-heroicon-s-check class="w-2.5 h-2.5 text-primary-content" />
                                            @endif
                                        </div>
                                        <span class="text-xs {{ $estado === 'pendiente' ? 'text-primary font-medium' : 'text-base-content/40' }}">
                                            Pendiente
                                        </span>
                                    </div>

                                    {{-- Línea --}}
                                    <div class="flex-1 h-0.5 mb-4 mx-1 {{ in_array($estado, ['enviada','retornada']) ? 'bg-primary' : 'bg-base-300' }}"></div>

                                    {{-- Paso 2: Enviada --}}
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center
                                            {{ in_array($estado, ['enviada','retornada'])
                                                ? 'bg-primary border-primary'
                                                : 'border-base-300 bg-base-100' }}">
                                            @if($estado === 'retornada')
                                                <x-heroicon-s-check class="w-2.5 h-2.5 text-primary-content" />
                                            @endif
                                        </div>
                                        <span class="text-xs {{ $estado === 'enviada' ? 'text-primary font-medium' : 'text-base-content/40' }}">
                                            Enviada
                                        </span>
                                    </div>

                                    {{-- Línea --}}
                                    <div class="flex-1 h-0.5 mb-4 mx-1 {{ $estado === 'retornada' ? 'bg-primary' : 'bg-base-300' }}"></div>

                                    {{-- Paso 3: Retornada --}}
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center
                                            {{ $estado === 'retornada'
                                                ? 'bg-primary border-primary'
                                                : 'border-base-300 bg-base-100' }}">
                                        </div>
                                        <span class="text-xs {{ $estado === 'retornada' ? 'text-primary font-medium' : 'text-base-content/40' }}">
                                            Retornada
                                        </span>
                                    </div>
                                </div>

                                {{-- Acciones --}}
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.solicitudes.show', $solicitud->ID_SOLICITUD) }}"
                                        class="btn btn-outline btn-sm gap-1 flex-1">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                        Ver
                                    </a>
                                    @if($solicitud->isPendiente() || $solicitud->isEnviada())
                                        <a href="{{ route('admin.solicitudes.edit', $solicitud->ID_SOLICITUD) }}"
                                            class="btn btn-info btn-sm gap-1 flex-1">
                                            @if($solicitud->isPendiente())
                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                                Editar
                                            @else
                                                <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                                                Retornar
                                            @endif
                                        </a>
                                    @endif
                                    @if($solicitud->isPendiente())
                                        <form method="POST"
                                            action="{{ route('admin.solicitudes.destroy', $solicitud->ID_SOLICITUD) }}"
                                            onsubmit="return confirm('¿Eliminar esta solicitud? Se revertirán todas las cantidades.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-error btn-sm">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay solicitudes registradas.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $solicitudes->links() }}</div>

    <script>
        function toggleSolicitud(id) {
            const contenedor = document.getElementById(`solicitud-${id}`);
            const icono = document.getElementById(`icon-solicitud-${id}`);
            const estaOculto = contenedor.classList.contains('hidden');

            // Cerrar cualquier otra abierta
            document.querySelectorAll('[id^="solicitud-"]').forEach(el => {
                if (el.id !== `solicitud-${id}` && !el.classList.contains('hidden')) {
                    el.classList.add('hidden');
                    const otroId = el.id.replace('solicitud-', '');
                    const otroIcono = document.getElementById(`icon-solicitud-${otroId}`);
                    if (otroIcono) otroIcono.style.transform = 'rotate(0deg)';
                }
            });

            contenedor.classList.toggle('hidden', !estaOculto);
            if (icono) icono.style.transform = estaOculto ? 'rotate(90deg)' : 'rotate(0deg)';

            if (estaOculto) {
                setTimeout(() => {
                    const card = contenedor.closest('.solicitud-card');
                    if (card) {
                        const y = card.getBoundingClientRect().top + window.scrollY - 80;
                        window.scrollTo({ top: y, behavior: 'smooth' });
                    }
                }, 50);
            }
        }
    </script>

</x-app-layout>