<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Tipos de examen</h2>
            <a href="{{ route('admin.tipo-examenes.create') }}" class="btn btn-primary btn-sm gap-1">
                <x-heroicon-o-plus class="w-4 h-4" />
                <span class="hidden sm:inline">Nuevo tipo</span>
                <span class="sm:hidden">Nuevo</span>
            </a>
        </div>
    </x-slot>

    <x-alert />

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">

            {{-- VISTA DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Candidatos mín.</th>
                            <th>Días anticipación</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipos as $tipo)
                            <tr>
                                <td class="font-medium">{{ $tipo->nombre }}</td>
                                <td class="text-base-content/60 text-sm">{{ $tipo->descripcion ?? '—' }}</td>
                                <td class="font-mono">{{ $tipo->candidatos_minimos }}</td>
                                <td class="font-mono">{{ $tipo->dias_anticipacion }}</td>
                                <td>
                                    <span class="badge {{ $tipo->estado === 'activo' ? 'badge-success' : 'badge-error' }}">
                                        {{ $tipo->estado }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <a href="{{ route('admin.tipo-examenes.edit', $tipo) }}"
                                            class="btn btn-info btn-xs gap-1">
                                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                            Editar
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.tipo-examenes.destroy', $tipo) }}"
                                            onsubmit="return confirm('¿Eliminar este tipo de examen?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-error btn-xs">
                                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-base-content/50 py-8">
                                    No hay tipos de examen registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL --}}
            <div class="md:hidden divide-y divide-base-300">
                @forelse($tipos as $tipo)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $tipo->nombre }}</p>
                            @if($tipo->descripcion)
                                <p class="text-xs text-base-content/50 truncate">{{ $tipo->descripcion }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-1 text-xs text-base-content/50">
                                <span>Mín. {{ $tipo->candidatos_minimos }} candidatos</span>
                                <span>·</span>
                                <span>{{ $tipo->dias_anticipacion }} días anticip.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="badge badge-sm {{ $tipo->estado === 'activo' ? 'badge-success' : 'badge-error' }}">
                                {{ $tipo->estado }}
                            </span>
                            <a href="{{ route('admin.tipo-examenes.edit', $tipo) }}"
                                class="btn btn-ghost btn-xs btn-square">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                            </a>
                            <form method="POST"
                                action="{{ route('admin.tipo-examenes.destroy', $tipo) }}"
                                onsubmit="return confirm('¿Eliminar este tipo de examen?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-xs btn-square text-error">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8 text-sm">
                        No hay tipos de examen registrados.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $tipos->links() }}</div>

</x-app-layout>