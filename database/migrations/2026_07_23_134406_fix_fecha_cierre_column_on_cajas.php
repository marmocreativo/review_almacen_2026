<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_cajas', function (Blueprint $table) {
            $table->timestamp('FECHA_CIERRE')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('al_cajas', function (Blueprint $table) {
            $table->timestamp('FECHA_CIERRE')->nullable(false)->useCurrent()->change();
        });
    }
};