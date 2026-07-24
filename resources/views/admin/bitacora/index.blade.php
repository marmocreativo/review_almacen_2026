<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Bitácora de actividad</h2>
        </div>
    </x-slot>

    <x-alert />

    {{-- FILTROS --}}
    <form method="GET" class="card bg-base-100 shadow mb-4">
        <div class="card-body py-3">
            <div class="flex flex-wrap gap-2 items-end">
                <div class="form-control flex-1 min-w-[180px]">
                    <label class="label py-0"><span class="label-text text-xs">Buscar</span></label>
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Descripción..."
                        class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control w-44">
                    <label class="label py-0"><span class="label-text text-xs">Módulo</span></label>
                    <select name="modulo" class="select select-bordered select-sm w-full">
                        <option value="">Todos</option>
                        @foreach($modulos as $modulo)
                            <option value="{{ $modulo }}" {{ request('modulo') === $modulo ? 'selected' : '' }}>
                                {{ ucfirst($modulo) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-44">
                    <label class="label py-0"><span class="label-text text-xs">Acción</span></label>
                    <select name="accion" class="select select-bordered select-sm w-full">
                        <option value="">Todas</option>
                        @foreach($acciones as $accion)
                            <option value="{{ $accion }}" {{ request('accion') === $accion ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $accion)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-44">
                    <label class="label py-0"><span class="label-text text-xs">Usuario</span></label>
                    <select name="id_user" class="select select-bordered select-sm w-full">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ (string) request('id_user') === (string) $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-40">
                    <label class="label py-0"><span class="label-text text-xs">Desde</span></label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="input input-bordered input-sm w-full" />
                </div>
                <div class="form-control w-40">
                    <label class="label py-0"><span class="label-text text-xs">Hasta</span></label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="input input-bordered input-sm w-full" />
                </div>
                <button type="submit" class="btn btn-primary btn-sm gap-1">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    Filtrar
                </button>
                <a href="{{ route('admin.bitacora.index') }}" class="btn btn-ghost btn-sm gap-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    Limpiar
                </a>
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
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>Detalle</th>
                            <th class="text-right">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $registro)
                            @php
                                $badge = str_contains($registro->accion, 'eliminar')
                                    ? 'badge-error'
                                    : (str_contains($registro->accion, 'destruida') ? 'badge-neutral' : 'badge-warning');
                            @endphp
                            <tr>
                                <td class="text-sm whitespace-nowrap">{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $registro->usuario?->name ?? 'Sistema' }}</td>
                                <td><span class="badge badge-ghost">{{ ucfirst($registro->modulo) }}</span></td>
                                <td><span class="badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $registro->accion)) }}</span></td>
                                <td class="max-w-xs">{{ $registro->descripcion }}</td>
                                <td class="max-w-sm">
                                    @if($registro->datos)
                                        <details class="text-xs">
                                            <summary class="cursor-pointer text-primary">Ver detalle</summary>
                                            <ul class="mt-1 space-y-0.5">
                                                @foreach($registro->datos as $clave => $valor)
                                                    <li>
                                                        <span class="text-base-content/50">{{ ucfirst(str_replace('_', ' ', $clave)) }}:</span>
                                                        @if(is_array($valor))
                                                            {{ implode(', ', array_slice($valor, 0, 10)) }}
                                                            @if(count($valor) > 10) <span class="text-base-content/40">(+{{ count($valor) - 10 }} más)</span> @endif
                                                        @else
                                                            {{ $valor }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </details>
                                    @else
                                        <span class="text-base-content/30">—</span>
                                    @endif
                                </td>
                                <td class="text-right text-xs text-base-content/50 whitespace-nowrap">{{ $registro->ip }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-base-content/50 py-8">
                                    No hay registros de actividad.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL --}}
            <div class="md:hidden divide-y divide-base-300">
                @forelse($registros as $registro)
                    @php
                        $badge = str_contains($registro->accion, 'eliminar')
                            ? 'badge-error'
                            : (str_contains($registro->accion, 'destruida') ? 'badge-neutral' : 'badge-warning');
                    @endphp
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="badge badge-xs {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $registro->accion)) }}</span>
                            <span class="text-xs text-base-content/40">{{ $registro->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-sm font-medium mt-1">{{ $registro->descripcion }}</p>
                        <p class="text-xs text-base-content/50 mt-0.5">
                            {{ $registro->usuario?->name ?? 'Sistema' }} · {{ ucfirst($registro->modulo) }}
                        </p>
                        @if($registro->datos)
                            <details class="text-xs mt-1">
                                <summary class="cursor-pointer text-primary">Ver detalle</summary>
                                <ul class="mt-1 space-y-0.5">
                                    @foreach($registro->datos as $clave => $valor)
                                        <li>
                                            <span class="text-base-content/50">{{ ucfirst(str_replace('_', ' ', $clave)) }}:</span>
                                            @if(is_array($valor))
                                                {{ implode(', ', array_slice($valor, 0, 10)) }}
                                                @if(count($valor) > 10) (+{{ count($valor) - 10 }} más) @endif
                                            @else
                                                {{ $valor }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">No hay registros de actividad.</div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $registros->links() }}</div>
</x-app-layout>