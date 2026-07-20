<x-app-layout>
    <div class="max-w-2xl mx-auto py-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold">Editar usuario</h1>
            <p class="text-base-content/60">{{ $user->name }}</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.usuarios.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="form-control">
                <label class="label" for="name">
                    <span class="label-text">Nombre</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="input input-bordered w-full @error('name') input-error @enderror"
                >
                @error('name')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control">
                <label class="label" for="email">
                    <span class="label-text">Correo electrónico</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="input input-bordered w-full @error('email') input-error @enderror"
                >
                @error('email')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control">
                <label class="label" for="rol">
                    <span class="label-text">Rol *</span>
                </label>
                <select id="rol" name="rol" class="select select-bordered w-full @error('rol') select-error @enderror">
                    <option value="">Selecciona un rol...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('rol', $user->userRole?->rol) == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->rol) }}
                        </option>
                    @endforeach
                </select>
                @error('rol')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="divider">Cambiar contraseña (opcional)</div>

            <div class="form-control">
                <label class="label" for="password">
                    <span class="label-text">Nueva contraseña</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Dejar en blanco para no cambiarla"
                    class="input input-bordered w-full @error('password') input-error @enderror"
                >
                @error('password')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control">
                <label class="label" for="password_confirmation">
                    <span class="label-text">Confirmar nueva contraseña</span>
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="input input-bordered w-full"
                >
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn btn-primary">
                    Guardar cambios
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-outline">
                    Cancelar
                </a>
            </div>
        </form>

    </div>
</x-app-layout>