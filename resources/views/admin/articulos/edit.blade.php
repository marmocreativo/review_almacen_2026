<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.articulos.show', $articulo) }}" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>
            <h2 class="text-xl font-semibold">
                Editar inventario — <span class="font-mono text-primary">{{ $articulo->FOLIO }}</span>
            </h2>
        </div>
    </x-slot>

    <x-alert />

    <div class="max-w-2xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">

                <form method="POST" action="{{ route('admin.articulos.update', $articulo) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Folio --}}
                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">ID Item *</span></label>
                            <input type="text" name="FOLIO"
                                value="{{ old('FOLIO', $articulo->FOLIO) }}"
                                class="input input-bordered @error('FOLIO') input-error @enderror"
                                required />
                            @error('FOLIO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Nombre --}}
                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Nombre *</span></label>
                            <input type="text" name="NOMBRE"
                                value="{{ old('NOMBRE', $articulo->NOMBRE) }}"
                                class="input input-bordered @error('NOMBRE') input-error @enderror"
                                required />
                            @error('NOMBRE')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Descripción</span></label>
                            <textarea name="DESCRIPCION" rows="2"
                                class="textarea textarea-bordered @error('DESCRIPCION') textarea-error @enderror">{{ old('DESCRIPCION', $articulo->DESCRIPCION) }}</textarea>
                            @error('DESCRIPCION')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Formato --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Formato</span></label>
                            <input type="text" name="FORMATO"
                                value="{{ old('FORMATO', $articulo->FORMATO) }}"
                                class="input input-bordered @error('FORMATO') input-error @enderror" />
                            @error('FORMATO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Tipo --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Tipo *</span></label>
                            <select name="TIPO" class="select select-bordered @error('TIPO') select-error @enderror" required>
                                <option value="fisico"  {{ old('TIPO', $articulo->TIPO) === 'fisico'  ? 'selected' : '' }}>Físico</option>
                                <option value="digital" {{ old('TIPO', $articulo->TIPO) === 'digital' ? 'selected' : '' }}>Digital</option>
                            </select>
                            @error('TIPO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Tipo de examen --}}
                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Tipo de examen relacionado</span></label>
                            <select name="ID_TIPO_EXAMEN" class="select select-bordered @error('ID_TIPO_EXAMEN') select-error @enderror">
                                <option value="">— Sin relación —</option>
                                @foreach($tiposExamen as $te)
                                    <option value="{{ $te->id }}" {{ old('ID_TIPO_EXAMEN', $articulo->ID_TIPO_EXAMEN) == $te->id ? 'selected' : '' }}>
                                        {{ $te->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ID_TIPO_EXAMEN')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Serie --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Folio</span></label>
                            <input type="text" name="SERIE"
                                value="{{ old('SERIE', $articulo->SERIE) }}"
                                class="input input-bordered @error('SERIE') input-error @enderror" />
                            @error('SERIE')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Núm. serie --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Folio (núm.)</span></label>
                            <input type="text" name="SERIE_NUMERICO"
                                value="{{ old('SERIE_NUMERICO', $articulo->SERIE_NUMERICO) }}"
                                class="input input-bordered @error('SERIE_NUMERICO') input-error @enderror" />
                            @error('SERIE_NUMERICO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Costo unitario --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Costo unitario</span></label>
                            <input type="number" step="0.01" min="0" name="COSTO_UNITARIO"
                                value="{{ old('COSTO_UNITARIO', $articulo->COSTO_UNITARIO) }}"
                                class="input input-bordered @error('COSTO_UNITARIO') input-error @enderror" />
                            @error('COSTO_UNITARIO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Precio venta --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Precio venta</span></label>
                            <input type="number" step="0.01" min="0" name="PRECIO_VENTA"
                                value="{{ old('PRECIO_VENTA', $articulo->PRECIO_VENTA) }}"
                                class="input input-bordered @error('PRECIO_VENTA') input-error @enderror" />
                            @error('PRECIO_VENTA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Cantidades (solo lectura) --}}
                        <div class="sm:col-span-2">
                            <div class="divider text-xs text-base-content/40">Cantidades (solo lectura)</div>
                            <div class="grid grid-cols-4 gap-3">
                                @foreach([
                                    ['label'=>'Almacén',    'value'=>$articulo->CANTIDAD_ALMACEN],
                                    ['label'=>'Solicitudes','value'=>$articulo->CANTIDAD_SOLICITUDES],
                                    ['label'=>'Destrucción','value'=>$articulo->CANTIDAD_DESTRUCCION],
                                    ['label'=>'Perdidos',   'value'=>$articulo->CANTIDAD_PERDIDOS],
                                ] as $c)
                                <div class="text-center bg-base-200 rounded-lg p-3">
                                    <p class="text-xs text-base-content/50">{{ $c['label'] }}</p>
                                    <p class="text-xl font-bold font-mono">{{ $c['value'] }}</p>
                                </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-base-content/40 mt-2">
                                Para modificar las cantidades usa el botón "+" en el listado de artículos.
                            </p>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('admin.articulos.show', $articulo) }}" class="btn btn-ghost">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Guardar cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>