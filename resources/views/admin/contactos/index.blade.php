<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.empresas.sedes.index', $empresa) }}" class="btn btn-ghost btn-sm">←</a>
                <h2 class="text-xl font-semibold">Contactos — {{ $sede->nombre }}</h2>
            </div>
            <a href="{{ route('admin.empresas.sedes.contactos.create', [$empresa, $sede]) }}"
                class="btn btn-primary btn-sm">
                + Nuevo contacto
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
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contactos as $contacto)
                        <tr>
                            <td class="font-medium">{{ $contacto->nombre }} {{ $contacto->apellidos }}</td>
                            <td>{{ $contacto->telefono ?? '—' }}</td>
                            <td>{{ $contacto->correo ?? '—' }}</td>
                            <td>
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('admin.empresas.sedes.contactos.edit', [$empresa, $sede, $contacto]) }}"
                                        class="btn btn-ghost btn-xs">Editar</a>
                                    <form method="POST"
                                        action="{{ route('admin.empresas.sedes.contactos.destroy', [$empresa, $sede, $contacto]) }}"
                                        onsubmit="return confirm('¿Eliminar este contacto?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-ghost btn-xs text-error">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-base-content/50 py-8">
                                No hay contactos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $contactos->links() }}
    </div>
</x-app-layout>