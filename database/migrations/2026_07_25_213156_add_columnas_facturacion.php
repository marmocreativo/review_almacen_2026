<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->string('FOLIO_FACTURA')->nullable()->after('IMPORTE_FACTURA');
        });

        Schema::table('al_solicitud_pagos', function (Blueprint $table) {
            $table->string('FORMA_PAGO')->nullable()->after('IMPORTE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['FOLIO_FACTURA']);
        });

        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['FORMA_PAGO']);
        });
    }
};
