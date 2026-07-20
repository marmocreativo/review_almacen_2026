<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.empresas.sedes.index', $empresa) }}" class="btn btn-ghost btn-sm">←</a>
            <h2 class="text-xl font-semibold">Nueva sede — {{ $empresa->nombre }}</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.empresas.sedes.store', $empresa) }}">
                @csrf

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text font-medium">Nombre *</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                        class="input input-bordered w-full @error('nombre') input-error @enderror" />
                    @error('nombre')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="divider text-sm text-base-content/40 my-2">Dirección</div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Calle y número</span></label>
                        <input type="text" name="calle_y_numero" value="{{ old('calle_y_numero') }}"
                            class="input input-bordered w-full @error('calle_y_numero') input-error @enderror" />
                        @error('calle_y_numero')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Colonia / Barrio</span></label>
                        <input type="text" name="colonia_barrio" value="{{ old('colonia_barrio') }}"
                            class="input input-bordered w-full @error('colonia_barrio') input-error @enderror" />
                        @error('colonia_barrio')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Alcaldía / Municipio</span></label>
                        <input type="text" name="alcaldia_municipio" value="{{ old('alcaldia_municipio') }}"
                            class="input input-bordered w-full @error('alcaldia_municipio') input-error @enderror" />
                        @error('alcaldia_municipio')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Ciudad</span></label>
                        <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                            class="input input-bordered w-full @error('ciudad') input-error @enderror" />
                        @error('ciudad')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Estado de la república</span></label>
                        <input type="text" name="estado_republica" value="{{ old('estado_republica') }}"
                            class="input input-bordered w-full @error('estado_republica') input-error @enderror" />
                        @error('estado_republica')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Código postal</span></label>
                        <input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}"
                            class="input input-bordered w-full @error('codigo_postal') input-error @enderror" />
                        @error('codigo_postal')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text font-medium">Referencias</span></label>
                    <textarea name="referencias" rows="2"
                        class="textarea textarea-bordered w-full @error('referencias') textarea-error @enderror">{{ old('referencias') }}</textarea>
                    @error('referencias')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-6">
                    <label class="label"><span class="label-text font-medium">Estado</span></label>
                    <select name="estado" class="select select-bordered w-full">
                        <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('admin.empresas.sedes.index', $empresa) }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>