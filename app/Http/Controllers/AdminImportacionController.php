<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AdminImportacionController extends Controller
{
    public function index()
    {
        return view('admin.importacion.index');
    }

    // ── DESCARGAR PLANTILLA ──
    public function descargarPlantilla(string $tipo)
    {
        $wb = new Spreadsheet();
        $ws = $wb->getActiveSheet();

        if ($tipo === 'inventario') {
            $ws->setTitle('Inventario');
            $headers = ['ID Item', 'Folio', 'Nombre', 'Descripción', 'Formato', 'Tipo', 'Costo unitario', 'Precio venta', 'Cantidad almacén'];
            $ejemplo = ['', 'S451232154', 'Cuadernillo TOEIC', '', 'CUADERNILLO', 'fisico', 45.50, 0, 1];
            $ejemplo2 = ['PZ-AUDIO', '', 'Audio USB', '', 'USB', 'digital', 0, 0, 25];
            $filename = 'plantilla_inventario.xlsx';
        } elseif ($tipo === 'empresas') {
            $ws->setTitle('Empresas-Sedes-Contactos');
            $headers = ['Empresa', 'RFC', 'Sede', 'Calle y número', 'Colonia', 'Alcaldía/Municipio', 'Ciudad', 'Estado', 'C.P.', 'Contacto Nombre', 'Contacto Apellidos', 'Contacto Teléfono', 'Contacto Correo'];
            $ejemplo = ['Berlitz', 'BER900101ABC', 'Sede Polanco', 'Av. Presidente Masaryk 111', 'Polanco', 'Miguel Hidalgo', 'CDMX', 'CDMX', '11560', 'Juan', 'Pérez López', '5512345678', 'juan.perez@berlitz.com'];
            $ejemplo2 = null;
            $filename = 'plantilla_empresas.xlsx';
        } else {
            abort(404);
        }

        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $ws->setCellValue($cell, $header);
            $ws->getStyle($cell)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $ws->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F448A');
        }

        $ws->fromArray($ejemplo, null, 'A2');
        if ($ejemplo2) $ws->fromArray($ejemplo2, null, 'A3');

        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $col) {
            $ws->getColumnDimension($col)->setWidth(20);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        (IOFactory::createWriter($wb, 'Xlsx'))->save('php://output');
        exit;
    }

    // ── IMPORTAR INVENTARIO ──
    public function importarInventario(Request $request)
    {
        $request->validate(['archivo' => 'required|file|mimes:xlsx,xls']);

        $spreadsheet = IOFactory::load($request->file('archivo')->getPathname());
        $filas = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        $agregados = [];
        $errores   = [];

        foreach (array_slice($filas, 1) as $i => $fila) { // saltar encabezado
            $numFila = $i + 2;

            [$idItem, $folioSerie, $nombre, $descripcion, $formato, $tipo, $costo, $precio, $cantidad] = array_pad($fila, 9, null);

            if (empty($nombre)) continue; // fila vacía

            if (empty($tipo) || !in_array($tipo, ['fisico', 'digital'])) {
                $errores[] = "Fila {$numFila}: Tipo inválido o vacío (debe ser 'fisico' o 'digital').";
                continue;
            }

            $serieData = null;
            if (!empty($folioSerie)) {
                if (!preg_match('/^S(\d{9})(?:-\d+)?$/', strtoupper(trim($folioSerie)), $m)) {
                    $errores[] = "Fila {$numFila}: Folio '{$folioSerie}' no cumple el formato S + 9 dígitos.";
                    continue;
                }
                $serie = strtoupper(trim($folioSerie));
                if (Articulo::where('SERIE', $serie)->exists()) {
                    $errores[] = "Fila {$numFila}: El folio {$serie} ya existe, se omitió.";
                    continue;
                }
                $serieData = ['serie' => $serie, 'numerico' => $m[1]];
            }

            $idItemFinal = !empty($idItem) ? strtoupper(trim($idItem)) : $this->generarFolioAleatorio();

            Articulo::create([
                'FOLIO'            => $idItemFinal,
                'SERIE'            => $serieData['serie'] ?? '',
                'SERIE_NUMERICO'   => $serieData['numerico'] ?? '',
                'NOMBRE'           => trim($nombre),
                'DESCRIPCION'      => $descripcion ?? '',
                'FORMATO'          => strtoupper(trim($formato ?? '')),
                'COSTO_UNITARIO'   => is_numeric($costo) ? $costo : 0,
                'PRECIO_VENTA'     => is_numeric($precio) ? $precio : 0,
                'CANTIDAD_ALMACEN' => $serieData ? 1 : (is_numeric($cantidad) ? (int) $cantidad : 1),
                'CANTIDAD_SOLICITUDES' => 0,
                'CANTIDAD_DESTRUCCION' => 0,
                'CANTIDAD_PERDIDOS'    => 0,
                'UBICACION_UNICA'  => 'almacen',
                'TIPO'             => $tipo,
            ]);

            $agregados[] = $idItemFinal . ($serieData ? " ({$serieData['serie']})" : '');
        }

        return back()->with('resultado_importacion', [
            'tipo'       => 'Inventario',
            'agregados'  => $agregados,
            'errores'    => $errores,
        ]);
    }

    // ── IMPORTAR EMPRESAS / SEDES / CONTACTOS ──
    public function importarEmpresas(Request $request)
    {
        $request->validate(['archivo' => 'required|file|mimes:xlsx,xls']);

        $spreadsheet = IOFactory::load($request->file('archivo')->getPathname());
        $filas = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        $agregados = [];
        $errores   = [];

        foreach (array_slice($filas, 1) as $i => $fila) {
            $numFila = $i + 2;

            [$nombreEmpresa, $rfc, $nombreSede, $calle, $colonia, $alcaldia, $ciudad, $estadoRepublica, $cp, $contactoNombre, $contactoApellidos, $contactoTelefono, $contactoCorreo] = array_pad($fila, 13, null);

            if (empty($nombreEmpresa) || empty($nombreSede)) {
                $errores[] = "Fila {$numFila}: Empresa y Sede son obligatorias.";
                continue;
            }

            $empresa = Empresa::firstOrCreate(
                ['nombre' => trim($nombreEmpresa)],
                ['razon_social' => trim($nombreEmpresa), 'rfc' => $rfc ?? '', 'estado' => 'activo']
            );

            $sede = Sede::firstOrCreate(
                ['id_empresa' => $empresa->id, 'nombre' => trim($nombreSede)],
                [
                    'calle_y_numero'      => $calle ?? '',
                    'colonia_barrio'      => $colonia ?? '',
                    'alcaldia_municipio'  => $alcaldia ?? '',
                    'ciudad'              => $ciudad ?? '',
                    'estado_republica'    => $estadoRepublica ?? '',
                    'codigo_postal'       => $cp ?? '',
                    'estado'              => 'activo',
                ]
            );

            if (!empty($contactoNombre)) {
                $contacto = Contacto::where('id_empresa', $empresa->id)
                    ->where('nombre', trim($contactoNombre))
                    ->where('apellidos', $contactoApellidos ?? '')
                    ->first();

                if (!$contacto) {
                    $contacto = Contacto::create([
                        'id_empresa' => $empresa->id,
                        'nombre'     => trim($contactoNombre),
                        'apellidos'  => $contactoApellidos ?? '',
                        'telefono'   => $contactoTelefono ?? '',
                        'correo'     => $contactoCorreo ?? '',
                        'pin'        => Contacto::generarPinUnico(),
                    ]);
                }

                // Adjunta la sede al contacto sin duplicar (many-to-many)
                $contacto->sedes()->syncWithoutDetaching([$sede->id]);
            }

            $agregados[] = "{$nombreEmpresa} / {$nombreSede}" . ($contactoNombre ? " / {$contactoNombre}" : '');
        }

        return back()->with('resultado_importacion', [
            'tipo'       => 'Empresas / Sedes / Contactos',
            'agregados'  => $agregados,
            'errores'    => $errores,
        ]);
    }

    private function generarFolioAleatorio(): string
    {
        do {
            $folio = 'AUTO-' . strtoupper(Str::random(6));
        } while (Articulo::where('FOLIO', $folio)->exists());

        return $folio;
    }
}