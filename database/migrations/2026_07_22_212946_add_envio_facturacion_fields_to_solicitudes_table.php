<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            // ── Envío ──
            $table->enum('ENVIO_ZONA', ['cdmx_area_metropolitana', 'foraneo'])->nullable()->after('OBSERVACIONES');
            $table->string('ENVIO_EXAMEN')->nullable();
            $table->string('ENVIO_VERSION')->nullable();
            $table->unsignedInteger('ENVIO_NUMERO_HOJAS')->nullable();
            $table->string('ENVIO_PASS_USB')->nullable();
            $table->unsignedInteger('ENVIO_CANTIDAD_SOBRES')->nullable();
            $table->string('ENVIO_FOLIOS_AUDIO')->nullable();
            $table->date('ENVIO_FECHA_ENVIO')->nullable();
            $table->unsignedInteger('ENVIO_DIAS_PERMITIDO')->nullable();
            $table->string('ENVIO_PAQUETERIA')->nullable();
            $table->string('ENVIO_PAQUETERIA_GUIA')->nullable();
            $table->decimal('ENVIO_PAQUETERIA_COSTO', 10, 2)->nullable();
            $table->text('ENVIO_NOTAS')->nullable();

            // ── Facturación ──
            $table->date('FACTURACION_FECHA')->nullable();
            $table->unsignedInteger('FACTURACION_DIAS_CREDITO')->nullable();
            $table->text('FACTURACION_NOTAS')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->dropColumn([
                'ENVIO_ZONA', 'ENVIO_EXAMEN', 'ENVIO_VERSION', 'ENVIO_NUMERO_HOJAS',
                'ENVIO_PASS_USB', 'ENVIO_CANTIDAD_SOBRES', 'ENVIO_FOLIOS_AUDIO',
                'ENVIO_FECHA_ENVIO', 'ENVIO_DIAS_PERMITIDO', 'ENVIO_PAQUETERIA',
                'ENVIO_PAQUETERIA_GUIA', 'ENVIO_PAQUETERIA_COSTO', 'ENVIO_NOTAS',
                'FACTURACION_FECHA', 'FACTURACION_DIAS_CREDITO', 'FACTURACION_NOTAS',
            ]);
        });
    }
};