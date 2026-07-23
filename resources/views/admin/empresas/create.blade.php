<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.empresas.index') }}" class="btn btn-ghost btn-sm btn-square">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
            </a>
            <h2 class="text-xl font-semibold">Nuevo cliente</h2>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-3xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.empresas.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="divider text-sm text-base-content/40 my-0">Datos generales</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Nombre *</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                            class="input input-bordered w-full @error('nombre') input-error @enderror" />
                        @error('nombre')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Tipo de cliente *</span></label>
                        <select name="tipo_cliente" class="select select-bordered w-full @error('tipo_cliente') select-error @enderror">
                            <option value="corporativo" {{ old('tipo_cliente', 'corporativo') === 'corporativo' ? 'selected' : '' }}>Corporativo</option>
                            <option value="academico" {{ old('tipo_cliente') === 'academico' ? 'selected' : '' }}>Académico</option>
                            <option value="gobierno" {{ old('tipo_cliente') === 'gobierno' ? 'selected' : '' }}>Gobierno</option>
                        </select>
                        @error('tipo_cliente')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Estado</span></label>
                        <select name="estado" class="select select-bordered w-full">
                            <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Logo</span></label>
                        <input type="file" name="logo" accept="image/*"
                            class="file-input file-input-bordered w-full @error('logo') file-input-error @enderror" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/40">Opcional — PNG, JPG. Máx. 2MB</span>
                        </label>
                        @error('logo')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="divider text-sm text-base-content/40">Contacto operativo</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Nombre</span></label>
                        <input type="text" name="nombre_contacto_operativo" value="{{ old('nombre_contacto_operativo') }}"
                            class="input input-bordered w-full @error('nombre_contacto_operativo') input-error @enderror" />
                        @error('nombre_contacto_operativo')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Apellido</span></label>
                        <input type="text" name="apellido_contacto_operativo" value="{{ old('apellido_contacto_operativo') }}"
                            class="input input-bordered w-full @error('apellido_contacto_operativo') input-error @enderror" />
                        @error('apellido_contacto_operativo')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Teléfono</span></label>
                        <input type="text" name="telefono_contacto_operativo" value="{{ old('telefono_contacto_operativo') }}"
                            class="input input-bordered w-full @error('telefono_contacto_operativo') input-error @enderror" />
                        @error('telefono_contacto_operativo')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Correo</span></label>
                        <input type="email" name="email_contacto_operativo" value="{{ old('email_contacto_operativo') }}"
                            class="input input-bordered w-full @error('email_contacto_operativo') input-error @enderror" />
                        @error('email_contacto_operativo')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="divider text-sm text-base-content/40">Contacto de facturación</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Nombre</span></label>
                        <input type="text" name="nombre_contacto_facturacion" value="{{ old('nombre_contacto_facturacion') }}"
                            class="input input-bordered w-full @error('nombre_contacto_facturacion') input-error @enderror" />
                        @error('nombre_contacto_facturacion')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Apellido</span></label>
                        <input type="text" name="apellido_contacto_facturacion" value="{{ old('apellido_contacto_facturacion') }}"
                            class="input input-bordered w-full @error('apellido_contacto_facturacion') input-error @enderror" />
                        @error('apellido_contacto_facturacion')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Teléfono</span></label>
                        <input type="text" name="telefono_contacto_facturacion" value="{{ old('telefono_contacto_facturacion') }}"
                            class="input input-bordered w-full @error('telefono_contacto_facturacion') input-error @enderror" />
                        @error('telefono_contacto_facturacion')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Correo</span></label>
                        <input type="email" name="email_contacto_facturacion" value="{{ old('email_contacto_facturacion') }}"
                            class="input input-bordered w-full @error('email_contacto_facturacion') input-error @enderror" />
                        @error('email_contacto_facturacion')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="divider text-sm text-base-content/40">Contrato</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Fecha de contrato</span></label>
                        <input type="date" name="fecha_de_contrato" value="{{ old('fecha_de_contrato') }}"
                            class="input input-bordered w-full @error('fecha_de_contrato') input-error @enderror" />
                        @error('fecha_de_contrato')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Vigencia (años)</span></label>
                        <input type="number" min="1" name="vigencia_contrato" value="{{ old('vigencia_contrato') }}"
                            class="input input-bordered w-full @error('vigencia_contrato') input-error @enderror" />
                        @error('vigencia_contrato')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Días de crédito</span></label>
                        <input type="number" min="0" name="dias_de_credito" value="{{ old('dias_de_credito') }}"
                            class="input input-bordered w-full @error('dias_de_credito') input-error @enderror" />
                        @error('dias_de_credito')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="divider text-sm text-base-content/40">Datos fiscales</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">RFC</span></label>
                        <input type="text" name="rfc" value="{{ old('rfc') }}"
                            class="input input-bordered w-full @error('rfc') input-error @enderror" />
                        @error('rfc')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Uso de CFDI</span></label>
                        <input type="text" name="uso_de_cfdi" value="{{ old('uso_de_cfdi') }}"
                            class="input input-bordered w-full @error('uso_de_cfdi') input-error @enderror" />
                        @error('uso_de_cfdi')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Razón social</span></label>
                        <input type="text" name="razon_social" value="{{ old('razon_social') }}"
                            class="input input-bordered w-full @error('razon_social') input-error @enderror" />
                        @error('razon_social')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Dirección fiscal</span></label>
                        <textarea name="direccion_fiscal" rows="2"
                            class="textarea textarea-bordered w-full @error('direccion_fiscal') textarea-error @enderror">{{ old('direccion_fiscal') }}</textarea>
                        @error('direccion_fiscal')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-control sm:col-span-2">
                        <label class="label"><span class="label-text font-medium">Portal de facturación</span></label>
                        <input type="text" name="portal_de_facturacion" value="{{ old('portal_de_facturacion') }}"
                            class="input input-bordered w-full @error('portal_de_facturacion') input-error @enderror" />
                        @error('portal_de_facturacion')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="divider text-sm text-base-content/40">Notas</div>
                <div class="form-control mb-6">
                    <textarea name="notas" rows="3"
                        class="textarea textarea-bordered w-full @error('notas') textarea-error @enderror">{{ old('notas') }}</textarea>
                    @error('notas')<p class="text-error text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('admin.empresas.index') }}" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>