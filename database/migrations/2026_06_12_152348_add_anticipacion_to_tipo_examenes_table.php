<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_examenes', function (Blueprint $table) {
            $table->integer('candidatos_minimos')->default(1)->after('descripcion');
            $table->integer('dias_anticipacion')->default(1)->after('candidatos_minimos');
        });
    }

    public function down(): void
    {
        Schema::table('tipo_examenes', function (Blueprint $table) {
            $table->dropColumn(['candidatos_minimos', 'dias_anticipacion']);
        });
    }
};