<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('rol')->unique();
            $table->json('permisos');
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('rol');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rol')->references('id')->on('roles')->onDelete('restrict');
            $table->unique('id_user'); // un usuario, un rol
        });

        // Roles por default
        $ahora = now();

        $todasLasSecciones = [
            'dashboard', 'inventario', 'solicitudes', 'envios', 'devoluciones',
            'facturacion', 'cobranza', 'destruccion', 'ordenes',
            'empresas', 'tipo-examenes', 'usuarios',
        ];

        DB::table('roles')->insert([
            [
                'rol'        => 'admin',
                'permisos'   => json_encode($todasLasSecciones),
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'rol'        => 'ventas',
                'permisos'   => json_encode(['dashboard', 'solicitudes', 'envios', 'devoluciones']),
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'rol'        => 'finanzas',
                'permisos'   => json_encode(['dashboard', 'facturacion', 'cobranza']),
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ]);

        // Asignar rol admin a todos los usuarios existentes que no tengan rol
        $idAdmin = DB::table('roles')->where('rol', 'admin')->value('id');
        $usuariosSinRol = DB::table('users')
            ->whereNotIn('id', DB::table('user_roles')->pluck('id_user'))
            ->pluck('id');

        foreach ($usuariosSinRol as $idUser) {
            DB::table('user_roles')->insert([
                'id_user'    => $idUser,
                'rol'        => $idAdmin,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
};