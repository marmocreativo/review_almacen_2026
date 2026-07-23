<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Destrucción</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.destruccion.exportar') }}" class="btn btn-success btn-sm gap-1">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    <span class="hidden sm:inline">Exportar</span>
                </a>
                <form method="POST" action="{{ route('admin.destruccion.store') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Nueva caja
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <x-alert />

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Caja</th>
                            <th class="text-center">Artículos</th>
                            <th class="text-center">Cantidad total</th>
                            <th>Estado</th>
                            <th>Fecha cierre</th>
                            <th>Fecha destruida</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $caja)
                            <tr>
                                <td class="font-medium">{{ $caja->NOMBRE }}</td>
                                <td class="text-center">{{ $caja->articulos_count }}</td>
                                <td class="text-center">{{ $caja->articulos_sum_CANTIDAD_A_DESTRUCCION ?? 0 }}</td>
                                <td>
                                    @if($caja->estaDestruida())
                                        <span class="badge badge-error">Destruida</span>
                                    @elseif($caja->estaCerrada())
                                        <span class="badge badge-warning">Cerrada</span>
                                    @else
                                        <span class="badge badge-success">Abierta</span>
                                    @endif
                                </td>
                                <td class="text-sm">{{ $caja->FECHA_CIERRE?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="text-sm">{{ $caja->FECHA_DESTRUIDA?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <a href="{{ route('admin.destruccion.caja', $caja) }}"
                                            class="btn btn-ghost btn-xs">Ver contenido</a>

                                        @if(!$caja->estaCerrada())
                                            <form method="POST" action="{{ route('admin.destruccion.cerrar', $caja) }}"
                                                onsubmit="return confirm('¿Cerrar esta caja? Ya no se podrán agregar más artículos.')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-xs">Cerrar caja</button>
                                            </form>
                                        @elseif(!$caja->estaDestruida())
                                            <form method="POST" action="{{ route('admin.destruccion.destruida', $caja) }}"
                                                onsubmit="return confirm('¿Marcar esta caja como destruida?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-error btn-xs">Marcar destruida</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-base-content/50 py-8">
                                    No hay cajas de destrucción registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>