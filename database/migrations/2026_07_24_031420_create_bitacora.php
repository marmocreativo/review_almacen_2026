<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->string('modulo', 50);
            $table->string('accion', 50);
            $table->string('descripcion', 500);
            $table->unsignedBigInteger('id_registro')->nullable();
            $table->json('datos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['modulo', 'accion']);
            $table->index('id_registro');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};