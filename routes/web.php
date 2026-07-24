<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminEmpresaController;
use App\Http\Controllers\AdminSedeController;
use App\Http\Controllers\AdminContactoController;
use App\Http\Controllers\AdminTipoExamenController;
use App\Http\Controllers\AdminArticuloController;
use App\Http\Controllers\AdminOrdenCompraController;
use App\Http\Controllers\AdminSolicitudController;
use App\Http\Controllers\AdminDestruccionController;
use App\Http\Controllers\AdminImportacionController;
use App\Http\Controllers\AdminUsuarioController;
use App\Http\Controllers\AdminRolController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\PortalSolicitudController;
use App\Http\Controllers\AdminBitacoraController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});



Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/buscar', [App\Http\Controllers\DashboardController::class, 'buscar'])->name('dashboard.buscar');
    Route::get('/dashboard/calendario', [App\Http\Controllers\DashboardController::class, 'calendario'])->name('dashboard.calendario');
    Route::get('dashboard/exportar', [App\Http\Controllers\DashboardController::class, 'exportar'])->name('dashboard.exportar');    

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/pin', [PinController::class, 'store'])->name('pin.store');
    Route::post('/pin/verificar', [PinController::class, 'verificar'])->name('pin.verificar');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::resource('roles', AdminRolController::class);

        Route::get('bitacora', [AdminBitacoraController::class, 'index'])->name('bitacora.index');

        Route::get('usuarios', [AdminUsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('usuarios/crear', [AdminUsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('usuarios', [AdminUsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('usuarios/{user}/edit', [AdminUsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::patch('usuarios/{user}', [AdminUsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('usuarios/{user}', [AdminUsuarioController::class, 'destroy'])->name('usuarios.destroy');
        

        Route::resource('empresas', AdminEmpresaController::class);
        Route::resource('empresas.sedes', AdminSedeController::class);
        Route::resource('empresas.contactos', AdminContactoController::class);
        Route::resource('tipo-examenes', AdminTipoExamenController::class, [
            'parameters' => ['tipo-examenes' => 'tipoExamen']
        ]);
        Route::post('empresas/{empresa}/contactos/{contacto}/enviar-pin', [AdminContactoController::class, 'enviarPin'])->name('empresas.contactos.enviar-pin');


        Route::get('articulos', [AdminArticuloController::class, 'index'])->name('articulos.index');
        Route::get('articulos/exportar', [AdminArticuloController::class, 'exportar'])->name('articulos.exportar');
        Route::post('articulos/buscar', [AdminArticuloController::class, 'buscar'])->name('articulos.buscar');
        Route::post('articulos/rango', [AdminArticuloController::class, 'storeRango'])->name('articulos.rango');
        Route::post('articulos/destroy-lote', [AdminArticuloController::class, 'destroyLote'])->name('articulos.destroy-lote');
        Route::post('articulos', [AdminArticuloController::class, 'store'])->name('articulos.store');
        Route::get('articulos/grupo/editar', [AdminArticuloController::class, 'editGrupo'])->name('articulos.grupo.edit');
        Route::patch('articulos/grupo', [AdminArticuloController::class, 'updateGrupo'])->name('articulos.grupo.update');
        Route::get('articulos/{articulo}', [AdminArticuloController::class, 'show'])->name('articulos.show');
        Route::get('articulos/{articulo}/edit', [AdminArticuloController::class, 'edit'])->name('articulos.edit');
        Route::put('articulos/{articulo}', [AdminArticuloController::class, 'update'])->name('articulos.update');
        Route::patch('articulos/{articulo}/inline', [AdminArticuloController::class, 'updateInline'])->name('articulos.inline');
        Route::post('articulos/{articulo}/agregar-almacen', [AdminArticuloController::class, 'agregarAlmacen'])->name('articulos.agregar-almacen');
        Route::delete('articulos/{articulo}', [AdminArticuloController::class, 'destroy'])->name('articulos.destroy');

        Route::get('ordenes', [AdminOrdenCompraController::class, 'index'])->name('ordenes.index');
         Route::post('ordenes/buscar-articulo', [AdminOrdenCompraController::class, 'buscarArticulo'])->name('ordenes.buscar-articulo');
        Route::post('ordenes/agregar-articulo', [AdminOrdenCompraController::class, 'agregarArticulo'])->name('ordenes.agregar-articulo');
        Route::delete('ordenes/articulo/{id}', [AdminOrdenCompraController::class, 'eliminarArticulo'])->name('ordenes.eliminar-articulo');
        Route::get('ordenes/crear', [AdminOrdenCompraController::class, 'create'])->name('ordenes.create');
        Route::post('ordenes', [AdminOrdenCompraController::class, 'store'])->name('ordenes.store');
        Route::get('ordenes/{orden}', [AdminOrdenCompraController::class, 'show'])->name('ordenes.show');
        Route::get('ordenes/{orden}/editar', [AdminOrdenCompraController::class, 'edit'])->name('ordenes.edit');
        Route::patch('ordenes/{orden}', [AdminOrdenCompraController::class, 'update'])->name('ordenes.update');
        Route::delete('ordenes/{orden}', [AdminOrdenCompraController::class, 'destroy'])->name('ordenes.destroy');
        Route::get('ordenes/{idOrden}/articulos', [AdminOrdenCompraController::class, 'articulos'])->name('ordenes.articulos');


        
        // Rutas sin parámetro dinámico — van primero
        Route::post('solicitudes/buscar-articulo', [AdminSolicitudController::class, 'buscarArticulo'])->name('solicitudes.buscar-articulo');
        Route::get('solicitudes/empresa/{empresa}/sedes', [AdminSolicitudController::class, 'getSedes'])->name('solicitudes.sedes');
        Route::get('solicitudes/sede/{sede}/contactos', [AdminSolicitudController::class, 'getContactos'])->name('solicitudes.contactos');

        // Secciones (listados filtrados sobre el mismo modelo Solicitud)
        Route::get('envios', [AdminSolicitudController::class, 'indexEnvios'])->name('envios.index')->middleware('permiso:envios');
        Route::get('devoluciones', [AdminSolicitudController::class, 'indexDevoluciones'])->name('devoluciones.index')->middleware('permiso:devoluciones');
        Route::get('facturacion', [AdminSolicitudController::class, 'indexFacturacion'])->name('facturacion.index')->middleware('permiso:facturacion');
        Route::get('cobranza', [AdminSolicitudController::class, 'indexCobranza'])->name('cobranza.index');
        Route::get('solicitudes/exportar', [AdminSolicitudController::class, 'exportarSolicitudes'])->name('solicitudes.exportar');
        Route::get('envios/exportar', [AdminSolicitudController::class, 'exportarEnvios'])->name('envios.exportar');
        Route::get('facturacion/exportar', [AdminSolicitudController::class, 'exportarFacturacion'])->name('facturacion.exportar');


        // Resource base (index, create, store, edit*, update*, destroy)
        Route::get('solicitudes', [AdminSolicitudController::class, 'index'])->name('solicitudes.index');
        Route::get('solicitudes/crear', [AdminSolicitudController::class, 'create'])->name('solicitudes.create');
        Route::post('solicitudes', [AdminSolicitudController::class, 'store'])->name('solicitudes.store');
        Route::delete('solicitudes/{solicitud}', [AdminSolicitudController::class, 'destroy'])->name('solicitudes.destroy');
        Route::patch('solicitudes/{solicitud}/estado', [AdminSolicitudController::class, 'cambiarEstado'])->name('solicitudes.estado');

        // ── Pestaña: Datos generales ──
        Route::get('solicitudes/{solicitud}', [AdminSolicitudController::class, 'showDatos'])->name('solicitudes.show');
        Route::patch('solicitudes/{solicitud}/datos', [AdminSolicitudController::class, 'updateDatos'])->name('solicitudes.datos.update');

        // ── Pestaña: Envío ──
        Route::get('solicitudes/{solicitud}/envio', [AdminSolicitudController::class, 'showEnvio'])->name('solicitudes.envio.show');
        Route::patch('solicitudes/{solicitud}/envio', [AdminSolicitudController::class, 'updateEnvio'])->name('solicitudes.envio.update');
        Route::post('solicitudes/{solicitud}/examenes', [AdminSolicitudController::class, 'agregarExamen'])->name('solicitudes.examenes.store');
        Route::delete('solicitudes/{solicitud}/examenes/{examen}', [AdminSolicitudController::class, 'eliminarExamen'])->name('solicitudes.examenes.destroy');
        Route::post('solicitudes/{solicitud}/examenes/{examen}/articulos', [AdminSolicitudController::class, 'agregarArticulo'])->name('solicitudes.articulos.store');
        Route::delete('solicitudes/{solicitud}/articulos/{articuloSolicitud}', [AdminSolicitudController::class, 'eliminarArticulo'])->name('solicitudes.articulos.destroy');
        Route::delete('solicitudes/{solicitud}/articulos-lote', [AdminSolicitudController::class, 'eliminarArticulosLote'])->name('solicitudes.articulos.destroy-lote');

        // ── Pestaña: Devolución ──
        Route::get('solicitudes/{solicitud}/devolucion', [AdminSolicitudController::class, 'showDevolucion'])->name('solicitudes.devolucion.show');
        Route::post('solicitudes/{solicitud}/devolucion/procesar', [AdminSolicitudController::class, 'procesarDevolucion'])->name('solicitudes.devolucion.procesar');

        // ── Pestaña: Facturación ──
        Route::get('solicitudes/{solicitud}/facturacion', [AdminSolicitudController::class, 'showFacturacion'])->name('solicitudes.facturacion.show');
        Route::patch('solicitudes/{solicitud}/facturacion', [AdminSolicitudController::class, 'updateFacturacion'])->name('solicitudes.facturacion.update');
        Route::post('solicitudes/{solicitud}/factura', [AdminSolicitudController::class, 'guardarFactura'])->name('solicitudes.factura');
        Route::patch('solicitudes/{solicitud}/vencimiento-cobranza', [AdminSolicitudController::class, 'guardarVencimiento'])->name('solicitudes.vencimiento-cobranza');
        Route::post('solicitudes/{solicitud}/pagos', [AdminSolicitudController::class, 'agregarPago'])->name('solicitudes.pagos.store');
        Route::delete('solicitudes/{solicitud}/pagos/{pago}', [AdminSolicitudController::class, 'eliminarPago'])->name('solicitudes.pagos.destroy');

        // ── PDF / Email (se mantienen) ──
        Route::get('solicitudes/{solicitud}/pdf', [AdminSolicitudController::class, 'generarPdf'])->name('solicitudes.pdf');
        Route::get('solicitudes/{solicitud}/carta', [AdminSolicitudController::class, 'generarCartaWord'])->name('solicitudes.carta');
        Route::post('solicitudes/{solicitud}/email', [AdminSolicitudController::class, 'enviarEmail'])->name('solicitudes.email');

        Route::get('importacion', [AdminImportacionController::class, 'index'])->name('importacion.index');
        Route::get('importacion/plantilla/{tipo}', [AdminImportacionController::class, 'descargarPlantilla'])->name('importacion.plantilla');
        Route::post('importacion/inventario', [AdminImportacionController::class, 'importarInventario'])->name('importacion.inventario');
        Route::post('importacion/empresas', [AdminImportacionController::class, 'importarEmpresas'])->name('importacion.empresas');

        Route::get('destruccion', [AdminDestruccionController::class, 'index'])->name('destruccion.index');
        Route::get('destruccion/exportar', [AdminDestruccionController::class, 'exportar'])->name('destruccion.exportar');
        Route::post('destruccion', [AdminDestruccionController::class, 'store'])->name('destruccion.store');
        Route::get('destruccion/{caja}', [AdminDestruccionController::class, 'contenidoCaja'])->name('destruccion.caja');
        Route::patch('destruccion/{caja}/cerrar', [AdminDestruccionController::class, 'cerrar'])->name('destruccion.cerrar');
        Route::patch('destruccion/{caja}/destruida', [AdminDestruccionController::class, 'marcarDestruida'])->name('destruccion.destruida');



       
    });
});

Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalSolicitudController::class, 'login'])->name('login');
    Route::post('/verificar', [PortalSolicitudController::class, 'verificarPin'])->name('verificar');
    Route::post('/solicitudes', [PortalSolicitudController::class, 'store'])->name('solicitudes.store');
    Route::get('/solicitudes/{solicitud}/examenes', [PortalSolicitudController::class, 'examenes'])->name('solicitudes.examenes');
    Route::post('/solicitudes/{solicitud}/examenes', [PortalSolicitudController::class, 'agregarExamen'])->name('solicitudes.examenes.store');
    Route::delete('/solicitudes/{solicitud}/examenes/{examen}', [PortalSolicitudController::class, 'eliminarExamen'])->name('solicitudes.examenes.destroy');
    Route::get('/solicitudes/{solicitud}/resumen', [PortalSolicitudController::class, 'resumen'])->name('solicitudes.resumen');
});

require __DIR__.'/auth.php';