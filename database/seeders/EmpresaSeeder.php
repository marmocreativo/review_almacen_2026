<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $empresas = [
            [
                'nombre'       => 'Universidad Anáhuac',
                'razon_social' => 'Universidad Anáhuac México S.C.',
                'rfc'          => 'UAM850312KT3',
                'logo'         => 'default.jpg',
                'estado'       => 'activo',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nombre'       => 'Tecnológico de Monterrey',
                'razon_social' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey',
                'rfc'          => 'ITE440223RX5',
                'logo'         => 'default.jpg',
                'estado'       => 'activo',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nombre'       => 'Berlitz México',
                'razon_social' => 'Berlitz de México S.A. de C.V.',
                'rfc'          => 'BME920615FJ2',
                'logo'         => 'default.jpg',
                'estado'       => 'activo',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        DB::table('empresas')->insert($empresas);
    }
}