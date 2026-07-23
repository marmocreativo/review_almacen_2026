<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.empresas.contactos.index', $empresa) }}" class="btn btn-ghost btn-sm btn-square">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
            </a>
            <h2 class="text-xl font-semibold">Editar contacto — {{ $empresa->nombre }}</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.empresas.contactos.update', [$empresa, $contacto]) }}">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nombre *</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre', $contacto->nombre) }}"
                            class="input input-bordered @error('nombre') input-error @enderror" />
                        @error('nombre')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Apellidos *</span></label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $contacto->apellidos) }}"
                            class="input input-bordered @error('apellidos') input-error @enderror" />
                        @error('apellidos')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Teléfono</span></label>
                        <input type="text" name="telefono" value="{{ old('telefono', $contacto->telefono) }}"
                            class="input input-bordered @error('telefono') input-error @enderror" />
                        @error('telefono')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Correo</span></label>
                        <input type="email" name="correo" value="{{ old('correo', $contacto->correo) }}"
                            class="input input-bordered @error('correo') input-error @enderror" />
                        @error('correo')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="divider text-sm text-base-content/40">Sedes asignadas</div>
                <div class="form-control mb-6">
                    @php $sedesAsignadas = old('sedes', $contacto->sedes->pluck('id')->toArray()); @endphp
                    @if($sedes->isEmpty())
                        <p class="text-sm text-base-content/50">Esta empresa no tiene sedes activas registradas.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($sedes as $sede)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="sedes[]" value="{{ $sede->id }}"
                                        class="checkbox checkbox-sm"
                                        {{ in_array($sede->id, $sedesAsignadas) ? 'checked' : '' }} />
                                    <span class="text-sm">{{ $sede->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('sedes')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('admin.empresas.contactos.index', $empresa) }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>