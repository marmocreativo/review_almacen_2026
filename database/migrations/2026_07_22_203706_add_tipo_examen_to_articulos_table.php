<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_articulos', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_TIPO_EXAMEN')->nullable()->after('TIPO');
            $table->foreign('ID_TIPO_EXAMEN')->references('id')->on('tipo_examenes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('al_articulos', function (Blueprint $table) {
            $table->dropForeign(['ID_TIPO_EXAMEN']);
            $table->dropColumn('ID_TIPO_EXAMEN');
        });
    }
};