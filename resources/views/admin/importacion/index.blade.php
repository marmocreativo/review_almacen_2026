<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Importación</h2>
    </x-slot>

    <x-alert />

    @if(session('resultado_importacion'))
        @php $r = session('resultado_importacion'); @endphp
        <div class="card bg-base-100 shadow mb-6">
            <div class="card-body">
                <h3 class="card-title text-base mb-2">Resultado: {{ $r['tipo'] }}</h3>
                <div class="alert alert-success mb-2">
                    <span>{{ count($r['agregados']) }} registro(s) importados correctamente.</span>
                </div>
                @if(count($r['errores']))
                    <div class="alert alert-warning">
                        <div>
                            <p class="font-medium mb-1">{{ count($r['errores']) }} fila(s) con errores u omitidas:</p>
                            <ul class="text-sm list-disc list-inside">
                                @foreach($r['errores'] as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- IMPORTAR INVENTARIO --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title text-base flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5" />
                    Inventario
                </h3>
                <p class="text-sm text-base-content/60 mb-3">
                    Da de alta artículos en lote. Deja el "ID Item" vacío para que se genere automáticamente.
                </p>
                <a href="{{ route('admin.importacion.plantilla', 'inventario') }}" class="btn btn-outline btn-sm gap-1 mb-4">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Descargar plantilla
                </a>
                <form method="POST" action="{{ route('admin.importacion.inventario') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-control mb-3">
                        <input type="file" name="archivo" accept=".xlsx,.xls" required
                            class="file-input file-input-bordered file-input-sm w-full" />
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                        Importar inventario
                    </button>
                </form>
            </div>
        </div>

        {{-- IMPORTAR EMPRESAS/SEDES/CONTACTOS --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title text-base flex items-center gap-2">
                    <x-heroicon-o-building-office-2 class="w-5 h-5" />
                    Empresas / Sedes / Contactos
                </h3>
                <p class="text-sm text-base-content/60 mb-3">
                    Una fila por contacto. Si la empresa o sede ya existen (por nombre), se reutilizan en vez de duplicarse.
                </p>
                <a href="{{ route('admin.importacion.plantilla', 'empresas') }}" class="btn btn-outline btn-sm gap-1 mb-4">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Descargar plantilla
                </a>
                <form method="POST" action="{{ route('admin.importacion.empresas') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-control mb-3">
                        <input type="file" name="archivo" accept=".xlsx,.xls" required
                            class="file-input file-input-bordered file-input-sm w-full" />
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm gap-1">
                        <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                        Importar empresas
                    </button>
                </form>
            </div>
        </div>

    </div>

</x-app-layout>