<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.ordenes.index') }}" class="btn btn-ghost btn-sm">←</a>
            <h2 class="text-xl font-semibold">Editar orden {{ $orden->ID_ORDEN }}</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ordenes.update', $orden->ID) }}"
                enctype="multipart/form-data">
                @csrf @method('PATCH')

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">ID Orden *</span></label>
                    <input type="text" name="ID_ORDEN" value="{{ old('ID_ORDEN', $orden->ID_ORDEN) }}"
                        class="input input-bordered @error('ID_ORDEN') input-error @enderror" />
                    @error('ID_ORDEN')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Folio factura</span></label>
                    <input type="text" name="FOLIO_FACTURA" value="{{ old('FOLIO_FACTURA', $orden->FOLIO_FACTURA) }}"
                        class="input input-bordered @error('FOLIO_FACTURA') input-error @enderror" />
                    @error('FOLIO_FACTURA')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Importe factura</span></label>
                    <label class="input input-bordered flex items-center gap-1 @error('IMPORTE_FACTURA') input-error @enderror">
                        <span class="text-base-content/40 text-sm">$</span>
                        <input type="number" step="0.01" min="0" name="IMPORTE_FACTURA"
                            value="{{ old('IMPORTE_FACTURA', $orden->IMPORTE_FACTURA) }}" class="grow" placeholder="0.00" />
                    </label>
                    @error('IMPORTE_FACTURA')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Fecha de registro *</span></label>
                    <input type="date" name="FECHA_REGISTRO"
                        value="{{ old('FECHA_REGISTRO', \Carbon\Carbon::parse($orden->FECHA_REGISTRO)->format('Y-m-d')) }}"
                        class="input input-bordered @error('FECHA_REGISTRO') input-error @enderror" />
                    @error('FECHA_REGISTRO')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Factura PDF</span></label>
                    @if($orden->FACTURA_PDF)
                        <a href="{{ asset('storage/' . $orden->FACTURA_PDF) }}" target="_blank"
                            class="badge badge-error mb-2">Ver PDF actual</a>
                    @endif
                    <input type="file" name="FACTURA_PDF" accept=".pdf"
                        class="file-input file-input-bordered w-full" />
                </div>

                <div class="form-control mb-6">
                    <label class="label"><span class="label-text">Factura XML</span></label>
                    @if($orden->FACTURA_XML)
                        <a href="{{ asset('storage/' . $orden->FACTURA_XML) }}" target="_blank"
                            class="badge badge-warning mb-2">Ver XML actual</a>
                    @endif
                    <input type="file" name="FACTURA_XML" accept=".xml"
                        class="file-input file-input-bordered w-full" />
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('admin.ordenes.index') }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>