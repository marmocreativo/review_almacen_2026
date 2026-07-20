<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">Órdenes de compra</h2>
            <a href="{{ route('admin.ordenes.create') }}" class="btn btn-primary btn-sm gap-1">
                <x-heroicon-o-plus class="w-4 h-4" />
                <span class="hidden sm:inline">Nueva orden</span>
                <span class="sm:hidden">Nueva</span>
            </a>
        </div>
    </x-slot>

    <x-alert />

    {{-- FILTROS --}}
    <form method="GET" class="card bg-base-100 shadow mb-4">
        <div class="card-body py-3">
            <div class="flex gap-2 items-end">
                <div class="form-control flex-1">
                    <label class="label py-0"><span class="label-text text-xs">Buscar</span></label>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                        placeholder="ID orden o folio factura..."
                        class="input input-bordered input-sm w-full" />
                </div>
                <button type="submit" class="btn btn-primary btn-sm gap-1">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    <span class="hidden sm:inline">Filtrar</span>
                </button>
                <a href="{{ route('admin.ordenes.index') }}" class="btn btn-ghost btn-sm gap-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    <span class="hidden sm:inline">Limpiar</span>
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
                            <th>ID Orden</th>
                            <th>Folio factura</th>
                            <th>Fecha registro</th>
                            <th>Artículos</th>
                            <th>Archivos</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenes as $orden)
                            <tr>
                                <td class="font-mono font-medium">{{ $orden->ID_ORDEN }}</td>
                                <td>{{ $orden->FOLIO_FACTURA ?: '—' }}</td>
                                <td>{{ \Carbon\Carbon::parse($orden->FECHA_REGISTRO)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.ordenes.articulos', $orden->ID_ORDEN) }}"
                                        class="badge badge-ghost">
                                        {{ $orden->articulos_count }} artículos
                                    </a>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        @if($orden->FACTURA_PDF)
                                            <a href="{{ asset('storage/' . $orden->FACTURA_PDF) }}"
                                                target="_blank" class="badge badge-error badge-sm">PDF</a>
                                        @endif
                                        @if($orden->FACTURA_XML)
                                            <a href="{{ asset('storage/' . $orden->FACTURA_XML) }}"
                                                target="_blank" class="badge badge-warning badge-sm">XML</a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <a href="{{ route('admin.ordenes.show', $orden->ID) }}"
                                            class="btn btn-ghost btn-xs gap-1">
                                            <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.ordenes.articulos', $orden->ID_ORDEN) }}"
                                            class="btn btn-outline btn-xs gap-1">
                                            <x-heroicon-o-archive-box class="w-3.5 h-3.5" />
                                            Artículos
                                        </a>
                                        <a href="{{ route('admin.ordenes.edit', $orden->ID) }}"
                                            class="btn btn-info btn-xs gap-1">
                                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                            Editar
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.ordenes.destroy', $orden->ID) }}"
                                            onsubmit="return confirm('¿Eliminar esta orden y sus artículos?')">
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
                                    No hay órdenes registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VISTA MÓVIL: tickets --}}
            <div class="md:hidden p-3 space-y-3">
                @forelse($ordenes as $orden)
                    <div class="rounded-xl border bg-base-100 border-base-300 overflow-hidden">

                        {{-- Cabecera del ticket --}}
                        <div class="bg-info/10 px-4 py-2.5 flex items-center justify-between">
                            <div>
                                <p class="font-mono font-bold text-info text-sm">{{ $orden->ID_ORDEN }}</p>
                                <p class="text-xs text-info/70">
                                    {{ \Carbon\Carbon::parse($orden->FECHA_REGISTRO)->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="flex gap-1.5">
                                @if($orden->FACTURA_PDF)
                                    <a href="{{ asset('storage/' . $orden->FACTURA_PDF) }}" target="_blank"
                                        class="badge badge-error badge-sm gap-1">
                                        <x-heroicon-o-document-arrow-down class="w-3 h-3" />
                                        PDF
                                    </a>
                                @endif
                                @if($orden->FACTURA_XML)
                                    <a href="{{ asset('storage/' . $orden->FACTURA_XML) }}" target="_blank"
                                        class="badge badge-warning badge-sm gap-1">
                                        <x-heroicon-o-code-bracket class="w-3 h-3" />
                                        XML
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Línea perforada --}}
                        <div class="relative flex items-center">
                            <div class="w-4 h-4 rounded-full bg-base-200 border border-base-300 -ml-2 shrink-0 z-10"></div>
                            <div class="flex-1 border-t-2 border-dashed border-base-300 mx-1"></div>
                            <div class="w-4 h-4 rounded-full bg-base-200 border border-base-300 -mr-2 shrink-0 z-10"></div>
                        </div>

                        {{-- Cuerpo del ticket --}}
                        <div class="px-4 py-3">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-xs text-base-content/50">Folio factura</span>
                                <span class="font-mono text-sm font-medium">
                                    {{ $orden->FOLIO_FACTURA ?: '—' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-base-content/50">Artículos</span>
                                <span class="font-mono text-sm font-medium">
                                    {{ $orden->articulos_count }}
                                </span>
                            </div>
                        </div>

                        {{-- Footer con acciones --}}
                        <div class="border-t border-base-300 px-3 py-2 flex gap-2">
                            <a href="{{ route('admin.ordenes.show', $orden->ID) }}"
                                class="btn btn-ghost btn-sm gap-1 flex-1">
                                <x-heroicon-o-eye class="w-4 h-4" />
                                Ver
                            </a>
                            <a href="{{ route('admin.ordenes.articulos', $orden->ID_ORDEN) }}"
                                class="btn btn-outline btn-sm gap-1 flex-1">
                                <x-heroicon-o-archive-box class="w-4 h-4" />
                                Artículos
                            </a>
                            <a href="{{ route('admin.ordenes.edit', $orden->ID) }}"
                                class="btn btn-info btn-sm gap-1 flex-1">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                                Editar
                            </a>
                            <form method="POST"
                                action="{{ route('admin.ordenes.destroy', $orden->ID) }}"
                                onsubmit="return confirm('¿Eliminar esta orden y sus artículos?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-error btn-sm">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-8">
                        No hay órdenes registradas.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="mt-4">{{ $ordenes->links() }}</div>

</x-app-layout>