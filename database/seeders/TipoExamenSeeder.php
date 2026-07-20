<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoExamenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipo_examenes')->insert([
            [
                'nombre'             => 'TOEIC Bridge',
                'descripcion'        => 'Examen de inglés para niveles básico e intermedio.',
                'candidatos_minimos' => 5,
                'dias_anticipacion'  => 10,
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'nombre'             => 'TOEIC L&R',
                'descripcion'        => 'Examen de Listening & Reading para nivel intermedio-avanzado.',
                'candidatos_minimos' => 5,
                'dias_anticipacion'  => 10,
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'nombre'             => 'TOEIC S&W',
                'descripcion'        => 'Examen de Speaking & Writing, evaluación oral y escrita.',
                'candidatos_minimos' => 3,
                'dias_anticipacion'  => 15,
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);
    }
}