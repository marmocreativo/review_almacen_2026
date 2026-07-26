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
            $table->enum('APROBACION', ['pendiente', 'aprobada', 'cancelada'])
                ->default('aprobada')
                ->after('ESTADO_SOLICITUD');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['APROBACION']);
        });
        
    }
};
