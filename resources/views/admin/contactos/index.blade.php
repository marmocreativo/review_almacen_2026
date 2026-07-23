<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.empresas.show', $empresa) }}" class="btn btn-ghost btn-sm btn-square">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <h2 class="text-xl font-semibold">Contactos — {{ $empresa->nombre }}</h2>
            </div>
            <a href="{{ route('admin.empresas.contactos.create', $empresa) }}" class="btn btn-primary btn-sm gap-1">
                <x-heroicon-o-plus class="w-4 h-4" />
                <span class="hidden sm:inline">Nuevo contacto</span>
                <span class="sm:hidden">Nuevo</span>
            </a>
        </div>
    </x-slot>

    <x-alert />

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">

            {{-- DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Sedes</th>
                            <th>PIN</th>
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
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($contacto->sedes as $sede)
                                            <span class="badge badge-ghost badge-sm">{{ $sede->nombre }}</span>
                                        @empty
                                            <span class="text-base-content/40 text-xs">Sin sedes</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    @if($contacto->pin)
                                        <div class="flex items-center gap-1">
                                            <span class="font-mono text-xs">{{ $contacto->pin }}</span>
                                            <button type="button" class="btn btn-ghost btn-xs btn-square"
                                                onclick="navigator.clipboard.writeText('{{ $contacto->pin }}'); this.querySelector('svg').classList.add('text-success')"
                                                title="Copiar PIN">
                                                <x-heroicon-o-clipboard class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-base-content/40 text-xs">Sin PIN</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <form method="POST" action="{{ route('admin.empresas.contactos.enviar-pin', [$empresa, $contacto]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-primary btn-xs gap-1" title="Enviar PIN por correo">
                                                <x-heroicon-o-envelope class="w-3.5 h-3.5" />
                                                PIN
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.empresas.contactos.edit', [$empresa, $contacto]) }}"
                                            class="btn btn-outline btn-info btn-xs gap-1">
                                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                            Editar
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.empresas.contactos.destroy', [$empresa, $contacto]) }}"
                                            onsubmit="return confirm('¿Eliminar este contacto?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline btn-error btn-xs gap-1">
                                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-base-content/50 py-8">
                                    No hay contactos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MÓVIL --}}
            <div class="md:hidden divide-y divide-base-300">
                @forelse($contactos as $contacto)
                    <div class="px-4 py-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-sm truncate">{{ $contacto->nombre }} {{ $contacto->apellidos }}</p>
                                <p class="text-xs text-base-content/50 truncate">{{ $contacto->correo ?? '—' }}</p>
                                <p class="text-xs text-base-content/50 truncate">{{ $contacto->telefono ?? '—' }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @forelse($contacto->sedes as $sede)
                                        <span class="badge badge-ghost badge-xs">{{ $sede->nombre }}</span>
                                    @empty
                                        <span class="text-base-content/40 text-xs">Sin sedes</span>
                                    @endforelse
                                </div>
                                @if($contacto->pin)
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="font-mono text-xs text-base-content/60">PIN: {{ $contacto->pin }}</span>
                                        <button type="button" class="btn btn-ghost btn-xs btn-square"
                                            onclick="navigator.clipboard.writeText('{{ $contacto->pin }}')"
                                            title="Copiar PIN">
                                            <x-heroicon-o-clipboard class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <a href="{{ route('admin.empresas.contactos.edit', [$empresa, $contacto]) }}"
                                    class="btn btn-ghost btn-xs btn-square">
                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                </a>
                                <form method="POST"
                                    action="{{ route('admin.empresas.contactos.destroy', [$empresa, $contacto]) }}"
                                    onsubmit="return confirm('¿Eliminar este contacto?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-xs btn-square text-error">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">No hay contactos registrados.</div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $contactos->links() }}</div>
</x-app-layout>