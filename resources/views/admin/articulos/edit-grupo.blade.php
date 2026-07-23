<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.articulos.index') }}" class="btn btn-ghost btn-sm">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                Volver
            </a>
            <h2 class="text-xl font-semibold">
                Editar en grupo — <span class="font-mono text-primary">{{ $primero->FOLIO }}</span>
                <span class="text-sm text-base-content/50">({{ $articulos->count() }} artículos)</span>
            </h2>
        </div>
    </x-slot>

    <x-alert />

    <div class="max-w-2xl">
        <div class="alert alert-warning mb-4">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
            <span>Estos cambios se aplicarán a los {{ $articulos->count() }} artículos seleccionados. Folio y número de serie no se modifican en esta vista.</span>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.articulos.grupo.update') }}">
                    @csrf @method('PATCH')

                    @foreach($ids as $id)
                        <input type="hidden" name="ids[]" value="{{ $id }}" />
                    @endforeach

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Nombre *</span></label>
                            <input type="text" name="NOMBRE" value="{{ old('NOMBRE', $primero->NOMBRE) }}"
                                class="input input-bordered @error('NOMBRE') input-error @enderror" required />
                            @error('NOMBRE')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Descripción</span></label>
                            <textarea name="DESCRIPCION" rows="2"
                                class="textarea textarea-bordered @error('DESCRIPCION') textarea-error @enderror">{{ old('DESCRIPCION', $primero->DESCRIPCION) }}</textarea>
                            @error('DESCRIPCION')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Formato</span></label>
                            <input type="text" name="FORMATO" value="{{ old('FORMATO', $primero->FORMATO) }}"
                                class="input input-bordered @error('FORMATO') input-error @enderror" />
                            @error('FORMATO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Tipo *</span></label>
                            <select name="TIPO" class="select select-bordered @error('TIPO') select-error @enderror" required>
                                <option value="fisico"  {{ old('TIPO', $primero->TIPO) === 'fisico'  ? 'selected' : '' }}>Físico</option>
                                <option value="digital" {{ old('TIPO', $primero->TIPO) === 'digital' ? 'selected' : '' }}>Digital</option>
                            </select>
                            @error('TIPO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Costo unitario</span></label>
                            <input type="number" step="0.01" min="0" name="COSTO_UNITARIO"
                                value="{{ old('COSTO_UNITARIO', $primero->COSTO_UNITARIO) }}"
                                class="input input-bordered @error('COSTO_UNITARIO') input-error @enderror" />
                            @error('COSTO_UNITARIO')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Precio venta</span></label>
                            <input type="number" step="0.01" min="0" name="PRECIO_VENTA"
                                value="{{ old('PRECIO_VENTA', $primero->PRECIO_VENTA) }}"
                                class="input input-bordered @error('PRECIO_VENTA') input-error @enderror" />
                            @error('PRECIO_VENTA')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Tipo de examen relacionado</span></label>
                            <select name="ID_TIPO_EXAMEN" class="select select-bordered @error('ID_TIPO_EXAMEN') select-error @enderror">
                                <option value="">— Sin relación —</option>
                                @foreach($tiposExamen as $te)
                                    <option value="{{ $te->id }}" {{ old('ID_TIPO_EXAMEN', $primero->ID_TIPO_EXAMEN) == $te->id ? 'selected' : '' }}>
                                        {{ $te->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ID_TIPO_EXAMEN')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('admin.articulos.index') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar cambios ({{ $articulos->count() }} artículos)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>