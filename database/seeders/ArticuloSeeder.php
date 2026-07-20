<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticuloSeeder extends Seeder
{
    public function run(): void
    {
        $ahora = now();
        $articulos = [];

        // ── 1. PLUMAS A GRANEL ──────────────────────────────────────────
        $articulos[] = [
            'FOLIO'                => 'PLU-001',
            'SERIE'                => '',
            'SERIE_NUMERICO'       => '',
            'NOMBRE'               => 'Pluma Bic Cristal Azul',
            'DESCRIPCION'          => 'Pluma de punta media, tinta azul, para uso en examen.',
            'FORMATO'              => 'PIEZA',
            'COSTO_UNITARIO'       => 2.50,
            'PRECIO_VENTA'         => 0.00,
            'CANTIDAD_ALMACEN'     => 500,
            'CANTIDAD_SOLICITUDES' => 0,
            'CANTIDAD_DESTRUCCION' => 0,
            'CANTIDAD_PERDIDOS'    => 0,
            'UBICACION_UNICA'      => 'almacen',
            'TIPO'                 => 'fisico',
            'FECHA_ACTUALIZACION'  => $ahora,
        ];

        $articulos[] = [
            'FOLIO'                => 'PLU-002',
            'SERIE'                => '',
            'SERIE_NUMERICO'       => '',
            'NOMBRE'               => 'Pluma Bic Cristal Negra',
            'DESCRIPCION'          => 'Pluma de punta media, tinta negra, para uso en examen.',
            'FORMATO'              => 'PIEZA',
            'COSTO_UNITARIO'       => 2.50,
            'PRECIO_VENTA'         => 0.00,
            'CANTIDAD_ALMACEN'     => 300,
            'CANTIDAD_SOLICITUDES' => 0,
            'CANTIDAD_DESTRUCCION' => 0,
            'CANTIDAD_PERDIDOS'    => 0,
            'UBICACION_UNICA'      => 'almacen',
            'TIPO'                 => 'fisico',
            'FECHA_ACTUALIZACION'  => $ahora,
        ];

        // ── 2. LIBRETAS A GRANEL ────────────────────────────────────────
        $articulos[] = [
            'FOLIO'                => 'LIB-001',
            'SERIE'                => '',
            'SERIE_NUMERICO'       => '',
            'NOMBRE'               => 'Libreta de Anotaciones Candidato',
            'DESCRIPCION'          => 'Libreta tamaño media carta, hojas lisas, para borrador del candidato.',
            'FORMATO'              => 'PIEZA',
            'COSTO_UNITARIO'       => 8.00,
            'PRECIO_VENTA'         => 0.00,
            'CANTIDAD_ALMACEN'     => 400,
            'CANTIDAD_SOLICITUDES' => 0,
            'CANTIDAD_DESTRUCCION' => 0,
            'CANTIDAD_PERDIDOS'    => 0,
            'UBICACION_UNICA'      => 'almacen',
            'TIPO'                 => 'fisico',
            'FECHA_ACTUALIZACION'  => $ahora,
        ];

        // ── 3. SOBRES DE SEGURIDAD A GRANEL ────────────────────────────
        $articulos[] = [
            'FOLIO'                => 'SOB-001',
            'SERIE'                => '',
            'SERIE_NUMERICO'       => '',
            'NOMBRE'               => 'Sobre de Seguridad Blanco Chico',
            'DESCRIPCION'          => 'Sobre de seguridad con sello adhesivo, tamaño 15x20 cm.',
            'FORMATO'              => 'PIEZA',
            'COSTO_UNITARIO'       => 4.00,
            'PRECIO_VENTA'         => 0.00,
            'CANTIDAD_ALMACEN'     => 600,
            'CANTIDAD_SOLICITUDES' => 0,
            'CANTIDAD_DESTRUCCION' => 0,
            'CANTIDAD_PERDIDOS'    => 0,
            'UBICACION_UNICA'      => 'almacen',
            'TIPO'                 => 'fisico',
            'FECHA_ACTUALIZACION'  => $ahora,
        ];

        $articulos[] = [
            'FOLIO'                => 'SOB-002',
            'SERIE'                => '',
            'SERIE_NUMERICO'       => '',
            'NOMBRE'               => 'Sobre de Seguridad Kraft Grande',
            'DESCRIPCION'          => 'Sobre de seguridad kraft con sello adhesivo, tamaño 25x35 cm.',
            'FORMATO'              => 'PIEZA',
            'COSTO_UNITARIO'       => 7.00,
            'PRECIO_VENTA'         => 0.00,
            'CANTIDAD_ALMACEN'     => 250,
            'CANTIDAD_SOLICITUDES' => 0,
            'CANTIDAD_DESTRUCCION' => 0,
            'CANTIDAD_PERDIDOS'    => 0,
            'UBICACION_UNICA'      => 'almacen',
            'TIPO'                 => 'fisico',
            'FECHA_ACTUALIZACION'  => $ahora,
        ];

        // ── 4. CUADERNILLOS CON SERIE ───────────────────────────────────
        // Bloque A: CUA-TOEICB-A0001 al A0040  (TOEIC Bridge)
        for ($i = 1; $i <= 40; $i++) {
            $numerico = (string) $i;
            $serie    = 'CUA-TOEICB-A' . str_pad($numerico, 4, '0', STR_PAD_LEFT);
            $articulos[] = [
                'FOLIO'                => 'CUA-TOEICB',
                'SERIE'                => $serie,
                'SERIE_NUMERICO'       => $numerico,
                'NOMBRE'               => 'Cuadernillo TOEIC Bridge',
                'DESCRIPCION'          => 'Cuadernillo de preguntas para examen TOEIC Bridge. Bloque A.',
                'FORMATO'              => 'CUADERNILLO',
                'COSTO_UNITARIO'       => 45.00,
                'PRECIO_VENTA'         => 0.00,
                'CANTIDAD_ALMACEN'     => 1,
                'CANTIDAD_SOLICITUDES' => 0,
                'CANTIDAD_DESTRUCCION' => 0,
                'CANTIDAD_PERDIDOS'    => 0,
                'UBICACION_UNICA'      => 'almacen',
                'TIPO'                 => 'fisico',
                'FECHA_ACTUALIZACION'  => $ahora,
            ];
        }

        // Bloque B: CUA-TOEICB-B0001 al B0030  (TOEIC Bridge, segunda versión)
        for ($i = 1; $i <= 30; $i++) {
            $numerico = (string) $i;
            $serie    = 'CUA-TOEICB-B' . str_pad($numerico, 4, '0', STR_PAD_LEFT);
            $articulos[] = [
                'FOLIO'                => 'CUA-TOEICB',
                'SERIE'                => $serie,
                'SERIE_NUMERICO'       => $numerico,
                'NOMBRE'               => 'Cuadernillo TOEIC Bridge',
                'DESCRIPCION'          => 'Cuadernillo de preguntas para examen TOEIC Bridge. Bloque B.',
                'FORMATO'              => 'CUADERNILLO',
                'COSTO_UNITARIO'       => 45.00,
                'PRECIO_VENTA'         => 0.00,
                'CANTIDAD_ALMACEN'     => 1,
                'CANTIDAD_SOLICITUDES' => 0,
                'CANTIDAD_DESTRUCCION' => 0,
                'CANTIDAD_PERDIDOS'    => 0,
                'UBICACION_UNICA'      => 'almacen',
                'TIPO'                 => 'fisico',
                'FECHA_ACTUALIZACION'  => $ahora,
            ];
        }

        // Bloque LR: CUA-TOEICLR-LR0001 al LR0030  (TOEIC L&R)
        for ($i = 1; $i <= 30; $i++) {
            $numerico = (string) $i;
            $serie    = 'CUA-TOEICLR-LR' . str_pad($numerico, 4, '0', STR_PAD_LEFT);
            $articulos[] = [
                'FOLIO'                => 'CUA-TOEICLR',
                'SERIE'                => $serie,
                'SERIE_NUMERICO'       => $numerico,
                'NOMBRE'               => 'Cuadernillo TOEIC L&R',
                'DESCRIPCION'          => 'Cuadernillo de preguntas para examen TOEIC Listening & Reading.',
                'FORMATO'              => 'CUADERNILLO',
                'COSTO_UNITARIO'       => 55.00,
                'PRECIO_VENTA'         => 0.00,
                'CANTIDAD_ALMACEN'     => 1,
                'CANTIDAD_SOLICITUDES' => 0,
                'CANTIDAD_DESTRUCCION' => 0,
                'CANTIDAD_PERDIDOS'    => 0,
                'UBICACION_UNICA'      => 'almacen',
                'TIPO'                 => 'fisico',
                'FECHA_ACTUALIZACION'  => $ahora,
            ];
        }

        DB::table('al_articulos')->insert($articulos);
    }
}