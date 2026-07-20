<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Destrucción</h2>
    </x-slot>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Ubicación / Caja</th>
                        <th class="text-center">Artículos</th>
                        <th class="text-center">Cantidad total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cajas as $caja)
                        <tr>
                            <td class="font-medium">{{ $caja->UBICACION_DESTRUCCION ?: '(Sin ubicación)' }}</td>
                            <td class="text-center">{{ $caja->total_articulos }}</td>
                            <td class="text-center">{{ $caja->total_cantidad }}</td>
                            <td>
                                <a href="{{ route('admin.destruccion.caja', ['caja' => $caja->UBICACION_DESTRUCCION]) }}"
                                    class="btn btn-ghost btn-xs">Ver contenido</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-base-content/50 py-8">
                                No hay artículos en destrucción.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>