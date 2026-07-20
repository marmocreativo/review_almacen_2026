<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('admin.tipo-examenes.index') }}" class="btn btn-ghost btn-sm btn-square shrink-0">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
            </a>
            <h2 class="text-xl font-semibold truncate">Nuevo tipo de examen</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.tipo-examenes.store') }}">
                @csrf

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text font-medium">Nombre *</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                        class="input input-bordered w-full @error('nombre') input-error @enderror" />
                    @error('nombre')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text font-medium">Descripción</span></label>
                    <textarea name="descripcion" rows="3"
                        class="textarea textarea-bordered w-full @error('descripcion') textarea-error @enderror">{{ old('descripcion') }}</textarea>
                    @error('descripcion')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Candidatos mínimos *</span></label>
                        <input type="number" name="candidatos_minimos" min="1"
                            value="{{ old('candidatos_minimos', 1) }}"
                            class="input input-bordered w-full @error('candidatos_minimos') input-error @enderror" />
                        @error('candidatos_minimos')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Días de anticipación *</span></label>
                        <input type="number" name="dias_anticipacion" min="1"
                            value="{{ old('dias_anticipacion', 1) }}"
                            class="input input-bordered w-full @error('dias_anticipacion') input-error @enderror" />
                        @error('dias_anticipacion')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Estado</span></label>
                        <select name="estado" class="select select-bordered w-full">
                            <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('admin.tipo-examenes.index') }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>