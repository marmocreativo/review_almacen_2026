<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->string('RESPONSABLE_TITULO')->nullable()->after('ID_CONTACTO');
            $table->date('FECHA_PRIMERA_APLICACION')->nullable()->after('SESIONES_SIMULTANEAS');
            $table->integer('CANTIDAD_SIMULTANEAS')->nullable()->after('SESIONES_SIMULTANEAS');
            $table->integer('CANTIDAD_USB')->nullable()->after('FECHA_PRIMERA_APLICACION');
            $table->integer('CANTIDAD_CD')->nullable()->after('CANTIDAD_USB');
        });
    }

    public function down(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['RESPONSABLE_TITULO', 'FECHA_PRIMERA_APLICACION', 'CANTIDAD_USB', 'CANTIDAD_CD', 'CANTIDAD_SIMULTANEAS']);
        });
    }
};