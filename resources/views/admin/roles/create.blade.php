<x-app-layout>
    <div class="max-w-2xl mx-auto py-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold">Crear rol</h1>
        </div>

        <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="form-control">
                <label class="label" for="rol"><span class="label-text">Nombre del rol *</span></label>
                <input type="text" id="rol" name="rol" value="{{ old('rol') }}"
                    class="input input-bordered w-full @error('rol') input-error @enderror" />
                @error('rol')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Secciones con acceso</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-base-200 rounded-lg p-4">
                    @foreach($secciones as $clave => $etiqueta)
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="permisos[]" value="{{ $clave }}"
                                {{ in_array($clave, old('permisos', [])) ? 'checked' : '' }}
                                class="checkbox checkbox-sm checkbox-primary" />
                            <span class="label-text text-sm">{{ $etiqueta }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn btn-primary">Crear rol</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>

    </div>
</x-app-layout>