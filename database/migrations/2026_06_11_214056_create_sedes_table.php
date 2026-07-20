<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa')->constrained('empresas')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('calle_y_numero')->nullable();
            $table->string('colonia_barrio')->nullable();
            $table->string('alcaldia_municipio')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado_republica')->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->text('referencias')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};