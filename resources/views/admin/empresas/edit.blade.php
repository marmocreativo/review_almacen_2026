<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.empresas.show', $empresa) }}" class="btn btn-ghost btn-sm btn-square">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
            </a>
            <h2 class="text-xl font-semibold truncate">{{ $empresa->nombre }}</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.empresas.update', $empresa) }}" enctype="multipart/form-data">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Nombre *</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}"
                            class="input input-bordered w-full @error('nombre') input-error @enderror" />
                        @error('nombre')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Razón social</span></label>
                        <input type="text" name="razon_social" value="{{ old('razon_social', $empresa->razon_social) }}"
                            class="input input-bordered w-full @error('razon_social') input-error @enderror" />
                        @error('razon_social')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">RFC</span></label>
                        <input type="text" name="rfc" value="{{ old('rfc', $empresa->rfc) }}"
                            class="input input-bordered w-full @error('rfc') input-error @enderror" />
                        @error('rfc')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Estado</span></label>
                        <select name="estado" class="select select-bordered w-full">
                            <option value="activo" {{ $empresa->estado === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ $empresa->estado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Logo</span></label>
                        @if($empresa->logo && $empresa->logo !== 'default.jpg')
                            <div class="mb-3 flex items-center gap-4">
                                <img src="{{ asset('storage/' . $empresa->logo) }}"
                                    onerror="this.src='https://placehold.co/64x64?text=E'"
                                    class="w-16 h-16 object-contain rounded border border-base-300" />
                                <span class="text-sm text-base-content/50">Logo actual</span>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="file-input file-input-bordered w-full @error('logo') file-input-error @enderror" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/40">
                                {{ $empresa->logo && $empresa->logo !== 'default.jpg' ? 'Subir un nuevo archivo reemplazará el actual.' : 'Opcional — PNG, JPG. Máx. 2MB' }}
                            </span>
                        </label>
                        @error('logo')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('admin.empresas.index') }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>