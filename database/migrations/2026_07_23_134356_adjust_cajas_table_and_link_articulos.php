<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_cajas', function (Blueprint $table) {
            if (!Schema::hasColumn('al_cajas', 'NUMERO')) {
                $table->unsignedInteger('NUMERO')->nullable()->after('NOMBRE');
            }
        });

        Schema::table('al_cajas', function (Blueprint $table) {
            if (!Schema::hasColumn('al_cajas', 'FECHA_DESTRUIDA')) {
                $table->timestamp('FECHA_DESTRUIDA')->nullable()->after('FECHA_CIERRE');
            }
        });

        Schema::table('al_solicitudes_articulos', function (Blueprint $table) {
            if (!Schema::hasColumn('al_solicitudes_articulos', 'ID_CAJA')) {
                $table->unsignedInteger('ID_CAJA')->nullable()->after('UBICACION_DESTRUCCION');
            }
        });
    }

    public function down(): void
    {
        Schema::table('al_solicitudes_articulos', function (Blueprint $table) {
            if (Schema::hasColumn('al_solicitudes_articulos', 'ID_CAJA')) {
                $table->dropColumn('ID_CAJA');
            }
        });

        Schema::table('al_cajas', function (Blueprint $table) {
            $cols = array_filter(['NUMERO', 'FECHA_DESTRUIDA'], fn($c) => Schema::hasColumn('al_cajas', $c));
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};