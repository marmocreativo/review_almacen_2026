<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.ordenes.index') }}" class="btn btn-ghost btn-sm">←</a>
            <h2 class="text-xl font-semibold">Nueva orden de compra</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ordenes.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">ID Orden *</span></label>
                    <input type="text" name="ID_ORDEN" value="{{ old('ID_ORDEN') }}"
                        class="input input-bordered @error('ID_ORDEN') input-error @enderror"
                        placeholder="Ej: 1001225" />
                    @error('ID_ORDEN')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Folio factura</span></label>
                    <input type="text" name="FOLIO_FACTURA" value="{{ old('FOLIO_FACTURA') }}"
                        class="input input-bordered @error('FOLIO_FACTURA') input-error @enderror" />
                    @error('FOLIO_FACTURA')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Importe factura</span></label>
                    <label class="input input-bordered flex items-center gap-1 @error('IMPORTE_FACTURA') input-error @enderror">
                        <span class="text-base-content/40 text-sm">$</span>
                        <input type="number" step="0.01" min="0" name="IMPORTE_FACTURA"
                            value="{{ old('IMPORTE_FACTURA') }}" class="grow" placeholder="0.00" />
                    </label>
                    @error('IMPORTE_FACTURA')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Fecha de registro *</span></label>
                    <input type="date" name="FECHA_REGISTRO" value="{{ old('FECHA_REGISTRO', date('Y-m-d')) }}"
                        class="input input-bordered @error('FECHA_REGISTRO') input-error @enderror" />
                    @error('FECHA_REGISTRO')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Factura PDF</span></label>
                    <input type="file" name="FACTURA_PDF" accept=".pdf"
                        class="file-input file-input-bordered w-full @error('FACTURA_PDF') file-input-error @enderror" />
                    @error('FACTURA_PDF')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-6">
                    <label class="label"><span class="label-text">Factura XML</span></label>
                    <input type="file" name="FACTURA_XML" accept=".xml"
                        class="file-input file-input-bordered w-full @error('FACTURA_XML') file-input-error @enderror" />
                    @error('FACTURA_XML')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" name="accion" value="salir" class="btn btn-primary">
                        Guardar y salir
                    </button>
                    <button type="submit" name="accion" value="articulos" class="btn btn-secondary">
                        Guardar y agregar artículos
                    </button>
                    <a href="{{ route('admin.ordenes.index') }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>