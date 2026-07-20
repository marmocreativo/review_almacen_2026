<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->date('FECHA_VENCIMIENTO_COBRANZA')->nullable()->after('IMPORTE_FACTURA');
        });

        Schema::create('al_solicitud_pagos', function (Blueprint $table) {
            $table->id('ID_PAGO');
            $table->integer('ID_SOLICITUD');
            $table->date('FECHA_PAGO');
            $table->decimal('IMPORTE', 10, 2);
            $table->text('NOTAS')->nullable();
            $table->timestamps();

            $table->foreign('ID_SOLICITUD')->references('ID_SOLICITUD')->on('al_solicitudes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('al_solicitud_pagos');
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->dropColumn('FECHA_VENCIMIENTO_COBRANZA');
        });
    }
};