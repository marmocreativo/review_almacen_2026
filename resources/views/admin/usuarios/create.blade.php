<x-app-layout>
    <div class="max-w-2xl mx-auto py-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold">Crear usuario</h1>
            <p class="text-base-content/60">Registra un nuevo usuario en el sistema</p>
        </div>

        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="form-control">
                <label class="label" for="name">
                    <span class="label-text">Nombre</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
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
                    value="{{ old('email') }}"
                    class="input input-bordered w-full @error('email') input-error @enderror"
                >
                @error('email')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control">
                <label class="label" for="password">
                    <span class="label-text">Contraseña</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="input input-bordered w-full @error('password') input-error @enderror"
                >
                @error('password')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control">
                <label class="label" for="password_confirmation">
                    <span class="label-text">Confirmar contraseña</span>
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
                    Crear usuario
                </button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline">
                    Cancelar
                </a>
            </div>
        </form>

    </div>
</x-app-layout>