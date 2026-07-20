<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.empresas.sedes.contactos.index', [$empresa, $sede]) }}" class="btn btn-ghost btn-sm">←</a>
            <h2 class="text-xl font-semibold">Nuevo contacto — {{ $sede->nombre }}</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.empresas.sedes.contactos.store', [$empresa, $sede]) }}">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nombre *</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                            class="input input-bordered @error('nombre') input-error @enderror" />
                        @error('nombre')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Apellidos *</span></label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                            class="input input-bordered @error('apellidos') input-error @enderror" />
                        @error('apellidos')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Teléfono</span></label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}"
                            class="input input-bordered @error('telefono') input-error @enderror" />
                        @error('telefono')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Correo</span></label>
                        <input type="email" name="correo" value="{{ old('correo') }}"
                            class="input input-bordered @error('correo') input-error @enderror" />
                        @error('correo')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('admin.empresas.sedes.contactos.index', [$empresa, $sede]) }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>