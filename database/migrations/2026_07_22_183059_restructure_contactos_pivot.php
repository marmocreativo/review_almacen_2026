<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar id_empresa a contactos (nullable primero para poder rellenar)
        Schema::table('contactos', function (Blueprint $table) {
            $table->foreignId('id_empresa')->nullable()->after('id')
                ->constrained('empresas')->cascadeOnDelete();
        });

        // 2. Crear tabla pivote
        Schema::create('contacto_sede', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contacto')->constrained('contactos')->cascadeOnDelete();
            $table->foreignId('id_sede')->constrained('sedes')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['id_contacto', 'id_sede']);
        });

        // 3. Migrar datos existentes: id_sede -> pivote, y backfill id_empresa
        DB::table('contactos')->orderBy('id')->chunk(100, function ($contactos) {
            foreach ($contactos as $contacto) {
                if ($contacto->id_sede) {
                    DB::table('contacto_sede')->insert([
                        'id_contacto' => $contacto->id,
                        'id_sede'     => $contacto->id_sede,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);

                    $sede = DB::table('sedes')->find($contacto->id_sede);
                    if ($sede) {
                        DB::table('contactos')->where('id', $contacto->id)
                            ->update(['id_empresa' => $sede->id_empresa]);
                    }
                }
            }
        });

        // 4. Quitar la columna id_sede de contactos (ya no aplica 1:N)
        Schema::table('contactos', function (Blueprint $table) {
            $table->dropForeign(['id_sede']);
            $table->dropColumn('id_sede');
        });
    }

    public function down(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->foreignId('id_sede')->nullable()->after('id_empresa')
                ->constrained('sedes')->cascadeOnDelete();
        });

        DB::table('contacto_sede')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('contactos')->where('id', $row->id_contacto)
                    ->update(['id_sede' => $row->id_sede]);
            }
        });

        Schema::dropIfExists('contacto_sede');

        Schema::table('contactos', function (Blueprint $table) {
            $table->dropForeign(['id_empresa']);
            $table->dropColumn('id_empresa');
        });
    }
};