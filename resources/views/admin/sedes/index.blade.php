<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.empresas.index') }}" class="btn btn-ghost btn-sm">←</a>
                <h2 class="text-xl font-semibold">Sedes de {{ $empresa->nombre }}</h2>
            </div>
            <a href="{{ route('admin.empresas.sedes.create', $empresa) }}" class="btn btn-primary btn-sm">
                + Nueva sede
            </a>
        </div>
    </x-slot>

    <x-alert />

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Ciudad</th>
                        <th>Contactos</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sedes as $sede)
                        <tr>
                            <td class="font-medium">{{ $sede->nombre }}</td>
                            <td>{{ $sede->calle_y_numero ?? '—' }}</td>
                            <td>{{ $sede->ciudad ?? '—' }}</td>
                            <td>
                                <a href="{{ route('admin.empresas.sedes.contactos.index', [$empresa, $sede]) }}"
                                    class="badge badge-ghost">
                                    {{ $sede->contactos_count }} contactos
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $sede->estado === 'activo' ? 'badge-success' : 'badge-error' }}">
                                    {{ $sede->estado }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('admin.empresas.sedes.edit', [$empresa, $sede]) }}"
                                        class="btn btn-ghost btn-xs">Editar</a>
                                    <form method="POST"
                                        action="{{ route('admin.empresas.sedes.destroy', [$empresa, $sede]) }}"
                                        onsubmit="return confirm('¿Eliminar esta sede?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-ghost btn-xs text-error">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-base-content/50 py-8">
                                No hay sedes registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $sedes->links() }}
    </div>
</x-app-layout>