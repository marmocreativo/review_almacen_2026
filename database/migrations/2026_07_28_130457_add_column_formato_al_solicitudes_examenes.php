<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_solicitudes_examenes', function (Blueprint $table) {
            $table->string('FORMATO')->nullable()->after('CANTIDAD');
        });
    }

    public function down(): void
    {
        Schema::table('al_solicitudes_examenes', function (Blueprint $table) {
            $table->dropColumn('FORMATO');
        });
    }
};