<!DOCTYPE html>
<html lang="es" data-theme="rq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Solicitud — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen p-4">
    <div class="max-w-2xl mx-auto py-6">
        <h2 class="text-xl font-semibold mb-1">Nueva solicitud</h2>
        <p class="text-sm text-base-content/50 mb-6">{{ $contacto->nombre }} {{ $contacto->apellidos }} — {{ $contacto->empresa?->nombre }}</p>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('portal.solicitudes.store') }}">
                    @csrf
                    <input type="hidden" name="pin" value="{{ $pin }}" />
                    <input type="hidden" name="contacto_id" value="{{ $contacto->id }}" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Sede *</span></label>
                            <select name="ID_SEDE" class="select select-bordered @error('ID_SEDE') select-error @enderror" required>
                                <option value="">Selecciona…</option>
                                @foreach($contacto->sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                            @error('ID_SEDE')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Zona de envío</span></label>
                            <select name="ENVIO_ZONA" class="select select-bordered">
                                <option value="">Selecciona…</option>
                                <option value="cdmx_area_metropolitana">CDMX / Área Metropolitana</option>
                                <option value="foraneo">Foráneo</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Sesiones simultáneas *</span></label>
                            <select name="SESIONES_SIMULTANEAS" class="select select-bordered" required>
                                <option value="no">No</option>
                                <option value="si">Sí</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Nombre del responsable *</span></label>
                            <input type="text" name="RESPONSABLE_NOMBRE" value="{{ old('RESPONSABLE_NOMBRE', $contacto->nombre . ' ' . $contacto->apellidos) }}"
                                class="input input-bordered @error('RESPONSABLE_NOMBRE') input-error @enderror" required />
                            @error('RESPONSABLE_NOMBRE')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Correo</span></label>
                            <input type="email" name="RESPONSABLE_CORREO" value="{{ old('RESPONSABLE_CORREO', $contacto->correo) }}"
                                class="input input-bordered" />
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Teléfono</span></label>
                            <input type="text" name="RESPONSABLE_TELEFONO" value="{{ old('RESPONSABLE_TELEFONO', $contacto->telefono) }}"
                                class="input input-bordered" />
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Celular</span></label>
                            <input type="text" name="RESPONSABLE_CELULAR" value="{{ old('RESPONSABLE_CELULAR') }}"
                                class="input input-bordered" />
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Dirección de envío</span></label>
                            <textarea name="DIRECCION_ENVIO" rows="2" class="textarea textarea-bordered">{{ old('DIRECCION_ENVIO') }}</textarea>
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Horario de atención</span></label>
                            <textarea name="HORARIO_DE_ATENCION" rows="2" class="textarea textarea-bordered">{{ old('HORARIO_DE_ATENCION') }}</textarea>
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Observaciones</span></label>
                            <textarea name="OBSERVACIONES" rows="2" class="textarea textarea-bordered">{{ old('OBSERVACIONES') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Continuar — agregar exámenes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>