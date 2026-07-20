<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SedeContactoSeeder extends Seeder
{
    public function run(): void
    {
        // Recuperamos los IDs por nombre para no asumir auto-incrementos
        $anahuac  = DB::table('empresas')->where('nombre', 'Universidad Anáhuac')->value('id');
        $tec      = DB::table('empresas')->where('nombre', 'Tecnológico de Monterrey')->value('id');
        $berlitz  = DB::table('empresas')->where('nombre', 'Berlitz México')->value('id');

        $sedes = [
            // Anáhuac
            [
                'id_empresa'         => $anahuac,
                'nombre'             => 'Campus Norte',
                'calle_y_numero'     => 'Av. Universidad Anáhuac 46',
                'colonia_barrio'     => 'Lomas Anáhuac',
                'alcaldia_municipio' => 'Huixquilucan',
                'ciudad'             => 'Estado de México',
                'estado_republica'   => 'Estado de México',
                'codigo_postal'      => '52786',
                'referencias'        => 'Frente a la capilla',
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id_empresa'         => $anahuac,
                'nombre'             => 'Campus Mayab',
                'calle_y_numero'     => 'Calle 13 Diagonal 96 No. 605',
                'colonia_barrio'     => 'Xcumpich',
                'alcaldia_municipio' => 'Mérida',
                'ciudad'             => 'Mérida',
                'estado_republica'   => 'Yucatán',
                'codigo_postal'      => '97119',
                'referencias'        => 'Junto al edificio de idiomas',
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            // Tec de Monterrey
            [
                'id_empresa'         => $tec,
                'nombre'             => 'Campus Monterrey',
                'calle_y_numero'     => 'Av. Eugenio Garza Sada 2501 Sur',
                'colonia_barrio'     => 'Tecnológico',
                'alcaldia_municipio' => 'Monterrey',
                'ciudad'             => 'Monterrey',
                'estado_republica'   => 'Nuevo León',
                'codigo_postal'      => '64849',
                'referencias'        => 'Edificio CEDES, planta baja',
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id_empresa'         => $tec,
                'nombre'             => 'Campus Santa Fe',
                'calle_y_numero'     => 'Av. Carlos Lazo 100',
                'colonia_barrio'     => 'Santa Fe',
                'alcaldia_municipio' => 'Álvaro Obregón',
                'ciudad'             => 'Ciudad de México',
                'estado_republica'   => 'CDMX',
                'codigo_postal'      => '01389',
                'referencias'        => 'Torre de rectoría, piso 3',
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id_empresa'         => $tec,
                'nombre'             => 'Campus Guadalajara',
                'calle_y_numero'     => 'Av. General Ramón Corona 2514',
                'colonia_barrio'     => 'Nuevo México',
                'alcaldia_municipio' => 'Zapopan',
                'ciudad'             => 'Guadalajara',
                'estado_republica'   => 'Jalisco',
                'codigo_postal'      => '45201',
                'referencias'        => 'Centro de idiomas, edificio B',
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            // Berlitz
            [
                'id_empresa'         => $berlitz,
                'nombre'             => 'Sucursal Polanco',
                'calle_y_numero'     => 'Presidente Masaryk 111 Piso 2',
                'colonia_barrio'     => 'Polanco V Sección',
                'alcaldia_municipio' => 'Miguel Hidalgo',
                'ciudad'             => 'Ciudad de México',
                'estado_republica'   => 'CDMX',
                'codigo_postal'      => '11560',
                'referencias'        => 'Entre Hegel y Arquímedes',
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id_empresa'         => $berlitz,
                'nombre'             => 'Sucursal Insurgentes',
                'calle_y_numero'     => 'Insurgentes Sur 1457 Piso 4',
                'colonia_barrio'     => 'Insurgentes Mixcoac',
                'alcaldia_municipio' => 'Benito Juárez',
                'ciudad'             => 'Ciudad de México',
                'estado_republica'   => 'CDMX',
                'codigo_postal'      => '03920',
                'referencias'        => 'Torre Siglum, frente al WTC',
                'estado'             => 'activo',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ];

        DB::table('sedes')->insert($sedes);

        // Ahora recuperamos los IDs de las sedes por nombre
        $sedeIds = DB::table('sedes')->pluck('id', 'nombre');

        $contactos = [
            // Anáhuac - Campus Norte
            [
                'id_sede'    => $sedeIds['Campus Norte'],
                'nombre'     => 'Lucía',
                'apellidos'  => 'Fernández Ramos',
                'telefono'   => '5550123401',
                'correo'     => 'lucia.fernandez@anahuac.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sede'    => $sedeIds['Campus Norte'],
                'nombre'     => 'Rodrigo',
                'apellidos'  => 'Villanueva Cruz',
                'telefono'   => '5550123402',
                'correo'     => 'rodrigo.villanueva@anahuac.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Anáhuac - Campus Mayab
            [
                'id_sede'    => $sedeIds['Campus Mayab'],
                'nombre'     => 'Sofía',
                'apellidos'  => 'Dzul Canché',
                'telefono'   => '9991234567',
                'correo'     => 'sofia.dzul@anahuac.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tec - Campus Monterrey
            [
                'id_sede'    => $sedeIds['Campus Monterrey'],
                'nombre'     => 'Alejandro',
                'apellidos'  => 'Garza Treviño',
                'telefono'   => '8181234567',
                'correo'     => 'a.garza@tec.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sede'    => $sedeIds['Campus Monterrey'],
                'nombre'     => 'Karla',
                'apellidos'  => 'Morales Ibarra',
                'telefono'   => '8187654321',
                'correo'     => 'k.morales@tec.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tec - Campus Santa Fe
            [
                'id_sede'    => $sedeIds['Campus Santa Fe'],
                'nombre'     => 'Diego',
                'apellidos'  => 'Sandoval Peña',
                'telefono'   => '5559876543',
                'correo'     => 'd.sandoval@tec.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tec - Campus Guadalajara
            [
                'id_sede'    => $sedeIds['Campus Guadalajara'],
                'nombre'     => 'Valentina',
                'apellidos'  => 'Ríos Esqueda',
                'telefono'   => '3331234567',
                'correo'     => 'v.rios@tec.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Berlitz - Polanco
            [
                'id_sede'    => $sedeIds['Sucursal Polanco'],
                'nombre'     => 'Fernanda',
                'apellidos'  => 'Acosta Leal',
                'telefono'   => '5552345678',
                'correo'     => 'facosta@berlitz.com.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sede'    => $sedeIds['Sucursal Polanco'],
                'nombre'     => 'Tomás',
                'apellidos'  => 'Herrera Bustamante',
                'telefono'   => '5552345679',
                'correo'     => 'therrera@berlitz.com.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Berlitz - Insurgentes
            [
                'id_sede'    => $sedeIds['Sucursal Insurgentes'],
                'nombre'     => 'Camila',
                'apellidos'  => 'Ortega Domínguez',
                'telefono'   => '5553456789',
                'correo'     => 'cortega@berlitz.com.mx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('contactos')->insert($contactos);
    }
}