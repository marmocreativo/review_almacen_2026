<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_solicitudes_articulos', function (Blueprint $table) {
            $table->enum('ESTADO_DEVOLUCION', ['aplicado', 'no_aplicado', 'danado', 'faltante'])
                ->nullable()->after('ESTADO');
        });
    }

    public function down(): void
    {
        Schema::table('al_solicitudes_articulos', function (Blueprint $table) {
            $table->dropColumn('ESTADO_DEVOLUCION');
        });
    }
};