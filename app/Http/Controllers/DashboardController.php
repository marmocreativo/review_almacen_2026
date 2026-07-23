<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Solicitud;
use App\Models\SolicitudArticulo;
use App\Models\OrdenCompra;
use App\Models\OrdenArticulo;
use App\Models\SolicitudExamen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ── Periodo ──
        $desde = $request->filled('desde')
            ? Carbon::parse($request->desde)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $hasta = $request->filled('hasta')
            ? Carbon::parse($request->hasta)->endOfDay()
            : Carbon::now()->endOfDay();

        // ── Total almacén (siempre global) ──
        $totalAlmacen = Articulo::sum('CANTIDAD_ALMACEN');
        $totalArticulos = Articulo::count();

        // ── Solicitudes del periodo ──
        $solicitudes = Solicitud::whereBetween('FECHA_SOLICITUD', [$desde, $hasta]);
        $totalSolicitudes   = (clone $solicitudes)->count();
        $importeSolicitudes = (clone $solicitudes)->sum('IMPORTE_FACTURA');

        $totalArticulosSolicitud = SolicitudArticulo::whereHas('solicitud', function ($q) use ($desde, $hasta) {
            $q->whereBetween('FECHA_SOLICITUD', [$desde, $hasta]);
        })->sum('CANTIDAD_ENVIADA');

        // ── Órdenes de compra del periodo ──
        $ordenes = OrdenCompra::whereBetween('FECHA_REGISTRO', [$desde, $hasta]);
        $totalOrdenes          = (clone $ordenes)->count();
        $importeOrdenes        = (clone $ordenes)->sum('IMPORTE_FACTURA');

        $totalArticulosOrdenes = OrdenArticulo::whereHas('orden', function ($q) use ($desde, $hasta) {
            $q->whereBetween('FECHA_REGISTRO', [$desde, $hasta]);
        })->sum('CANTIDAD');

        // ── Gráfica: solicitudes y órdenes por día/semana ──
        $diffDias = $desde->diffInDays($hasta);
        $agruparPor = $diffDias > 31 ? 'week' : 'day';

        $solicitudesPorPeriodo = Solicitud::whereBetween('FECHA_SOLICITUD', [$desde, $hasta])
            ->select(
                DB::raw($agruparPor === 'week'
                    ? 'YEARWEEK(FECHA_SOLICITUD, 1) as periodo, MIN(DATE(FECHA_SOLICITUD)) as fecha'
                    : 'DATE(FECHA_SOLICITUD) as periodo, DATE(FECHA_SOLICITUD) as fecha'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        $ordenesPorPeriodo = OrdenCompra::whereBetween('FECHA_REGISTRO', [$desde, $hasta])
            ->select(
                DB::raw($agruparPor === 'week'
                    ? 'YEARWEEK(FECHA_REGISTRO, 1) as periodo, MIN(DATE(FECHA_REGISTRO)) as fecha'
                    : 'DATE(FECHA_REGISTRO) as periodo, DATE(FECHA_REGISTRO) as fecha'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        // ── Top empresas con solicitudes ──
        $topEmpresas = Solicitud::whereBetween('FECHA_SOLICITUD', [$desde, $hasta])
            ->select('ID_EMPRESA', DB::raw('COUNT(*) as total'))
            ->with('empresa:id,nombre')
            ->groupBy('ID_EMPRESA')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Top tipos de examen ──
        $topExamenes = \App\Models\SolicitudExamen::whereHas('solicitud', function ($q) use ($desde, $hasta) {
            $q->whereBetween('FECHA_SOLICITUD', [$desde, $hasta]);
        })
        ->select('EXAMEN', DB::raw('COUNT(*) as total'), DB::raw('SUM(CANTIDAD) as candidatos'))
        ->groupBy('EXAMEN')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

        // ── Alertas / métricas extra ──
        $articulosPerdidos = SolicitudArticulo::whereHas('solicitud', function ($q) use ($desde, $hasta) {
            $q->whereBetween('FECHA_SOLICITUD', [$desde, $hasta]);
        })->sum('CANTIDAD_PERDIDOS');

        $articulosDestruccion = SolicitudArticulo::whereHas('solicitud', function ($q) use ($desde, $hasta) {
            $q->whereBetween('FECHA_SOLICITUD', [$desde, $hasta]);
        })->sum('CANTIDAD_A_DESTRUCCION');

        // Solicitudes enviadas con exámenes cuya fecha ya pasó
        $solicitudesVencidas = Solicitud::where('ESTADO_SOLICITUD', 'enviada')
            ->whereHas('examenes', function ($q) {
                $q->where('FECHA', '<', Carbon::today());
            })
            ->count();

        // ── Calendario de exámenes (mes actual) ──
        $mesCalendario = Carbon::now()->startOfMonth();
        $examenesCalendario = \App\Models\SolicitudExamen::with('solicitud.empresa:id,nombre')
            ->whereBetween('FECHA', [$mesCalendario, $mesCalendario->copy()->endOfMonth()])
            ->orderBy('FECHA')
            ->get()
            ->groupBy(fn($e) => Carbon::parse($e->FECHA)->format('Y-m-d'));
        $mesNombre = ucfirst($mesCalendario->translatedFormat('F Y'));
        $mesActual = $mesCalendario->format('Y-m');

        $pinConfigurado = auth()->user()->tienePin();

        return view('dashboard', compact(
            'desde', 'hasta', 'diffDias', 'agruparPor',
            'totalAlmacen', 'totalArticulos',
            'totalSolicitudes', 'importeSolicitudes', 'totalArticulosSolicitud',
            'totalOrdenes', 'importeOrdenes', 'totalArticulosOrdenes',
            'solicitudesPorPeriodo', 'ordenesPorPeriodo',
            'topEmpresas', 'topExamenes',
            'articulosPerdidos', 'articulosDestruccion', 'solicitudesVencidas',
            'examenesCalendario', 'mesNombre', 'mesActual',
            'pinConfigurado'
        ));
    }

    public function buscar(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return view('buscar', ['q' => $q, 'resultados' => null]);
        }

        $like = '%' . $q . '%';

        $articulos = \App\Models\Articulo::where('NOMBRE', 'like', $like)
            ->orWhere('FOLIO', 'like', $like)
            ->orWhere('SERIE', 'like', $like)
            ->orderBy('NOMBRE')
            ->limit(15)
            ->get();

        $solicitudes = \App\Models\Solicitud::with('empresa:id,nombre', 'sede:id,nombre')
            ->where(function ($query) use ($like, $q) {
                $query->where('RESPONSABLE_NOMBRE', 'like', $like)
                    ->orWhere('RESPONSABLE_CORREO', 'like', $like)
                    ->orWhere('ID_SOLICITUD', is_numeric($q) ? $q : -1);
            })
            ->orWhereHas('empresa', fn($eq) => $eq->where('nombre', 'like', $like))
            ->orderByDesc('FECHA_SOLICITUD')
            ->limit(10)
            ->get();

        $empresas = \App\Models\Empresa::where('nombre', 'like', $like)
            ->orWhere('razon_social', 'like', $like)
            ->orWhere('rfc', 'like', $like)
            ->orderBy('nombre')
            ->limit(10)
            ->get();

        $ordenes = \App\Models\OrdenCompra::where('FOLIO_FACTURA', 'like', $like)
            ->orWhere('ID_ORDEN', 'like', $like)
            ->orderByDesc('FECHA_REGISTRO')
            ->limit(10)
            ->get();

        return view('buscar', compact('q', 'articulos', 'solicitudes', 'empresas', 'ordenes'));
    }

    public function calendario(Request $request)
    {
        $mes = $request->filled('mes')
            ? Carbon::parse($request->mes . '-01')
            : Carbon::now()->startOfMonth();

        $inicio = $mes->copy()->startOfMonth();
        $fin    = $mes->copy()->endOfMonth();

        $examenes = \App\Models\SolicitudExamen::with('solicitud.empresa:id,nombre')
            ->whereBetween('FECHA', [$inicio, $fin])
            ->orderBy('FECHA')
            ->get()
            ->groupBy(fn($e) => Carbon::parse($e->FECHA)->format('Y-m-d'));

        return response()->json([
            'mes'        => $mes->format('Y-m'),
            'mes_nombre' => ucfirst($mes->translatedFormat('F Y')),
            'examenes'   => $examenes,
        ]);
    }

    public function exportar(Request $request)
    {
        $desde = $request->filled('desde')
            ? Carbon::parse($request->desde)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $hasta = $request->filled('hasta')
            ? Carbon::parse($request->hasta)->endOfDay()
            : Carbon::now()->endOfDay();

        $solicitudes = Solicitud::with(['empresa', 'sede', 'contacto'])
            ->withCount('examenes')
            ->withCount('articulos')
            ->whereBetween('FECHA_SOLICITUD', [$desde, $hasta])
            ->orderBy('FECHA_SOLICITUD', 'desc')
            ->get();

        $ordenes = OrdenCompra::withCount('articulos')
            ->whereBetween('FECHA_REGISTRO', [$desde, $hasta])
            ->orderBy('FECHA_REGISTRO', 'desc')
            ->get();

        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // ── HOJA 1: Solicitudes ──
        $ws1 = $wb->getActiveSheet();
        $ws1->setTitle('Solicitudes');

        $headers1 = ['#', 'Fecha', 'Empresa', 'Sede', 'Responsable', 'Exámenes', 'Artículos', 'Estado solicitud', 'Estado factura', 'Importe'];
        foreach ($headers1 as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $ws1->setCellValue($cell, $header);
            $ws1->getStyle($cell)->getFont()->setBold(true);
            $ws1->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E3A5F');
            $ws1->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
            $ws1->getStyle($cell)->getAlignment()->setHorizontal('center');
        }

        foreach ($solicitudes as $i => $s) {
            $row = $i + 2;
            $ws1->setCellValue("A{$row}", $s->ID_SOLICITUD);
            $ws1->setCellValue("B{$row}", Carbon::parse($s->FECHA_SOLICITUD)->format('d/m/Y'));
            $ws1->setCellValue("C{$row}", $s->empresa?->nombre ?? '—');
            $ws1->setCellValue("D{$row}", $s->sede?->nombre ?? '—');
            $ws1->setCellValue("E{$row}", $s->RESPONSABLE_NOMBRE);
            $ws1->setCellValue("F{$row}", $s->examenes_count);
            $ws1->setCellValue("G{$row}", $s->articulos_count);
            $ws1->setCellValue("H{$row}", $s->ESTADO_SOLICITUD);
            $ws1->setCellValue("I{$row}", $s->ESTADO_FACTURA);
            $ws1->setCellValue("J{$row}", $s->IMPORTE_FACTURA ?? 0);
            $ws1->getStyle("J{$row}")->getNumberFormat()->setFormatCode('"$"#,##0.00');

            // Color por estado
            $color = match($s->ESTADO_SOLICITUD) {
                'pendiente' => 'FFFFF3CD',
                'enviada'   => 'FFD1ECF1',
                'retornada' => 'FFD4EDDA',
                default     => 'FFFFFFFF',
            };
            $ws1->getStyle("A{$row}:J{$row}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($color);
        }

        // Totales hoja 1
        $lastRow1 = count($solicitudes) + 2;
        $ws1->setCellValue("I{$lastRow1}", 'TOTAL');
        $ws1->getStyle("I{$lastRow1}")->getFont()->setBold(true);
        $ws1->setCellValue("J{$lastRow1}", "=SUM(J2:J" . ($lastRow1 - 1) . ")");
        $ws1->getStyle("J{$lastRow1}")->getFont()->setBold(true);
        $ws1->getStyle("J{$lastRow1}")->getNumberFormat()->setFormatCode('"$"#,##0.00');

        // Anchos hoja 1
        foreach (['A'=>8,'B'=>12,'C'=>28,'D'=>22,'E'=>25,'F'=>10,'G'=>10,'H'=>16,'I'=>14,'J'=>14] as $col => $width) {
            $ws1->getColumnDimension($col)->setWidth($width);
        }

        // ── HOJA 2: Órdenes de compra ──
        $ws2 = $wb->createSheet();
        $ws2->setTitle('Órdenes de Compra');

        $headers2 = ['ID Orden', 'Fecha', 'Folio Factura', 'Artículos', 'Importe'];
        foreach ($headers2 as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $ws2->setCellValue($cell, $header);
            $ws2->getStyle($cell)->getFont()->setBold(true);
            $ws2->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E3A5F');
            $ws2->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
            $ws2->getStyle($cell)->getAlignment()->setHorizontal('center');
        }

        foreach ($ordenes as $i => $o) {
            $row = $i + 2;
            $ws2->setCellValue("A{$row}", $o->ID_ORDEN);
            $ws2->setCellValue("B{$row}", Carbon::parse($o->FECHA_REGISTRO)->format('d/m/Y'));
            $ws2->setCellValue("C{$row}", $o->FOLIO_FACTURA ?: '—');
            $ws2->setCellValue("D{$row}", $o->articulos_count);
            $ws2->setCellValue("E{$row}", $o->IMPORTE_FACTURA ?? 0);
            $ws2->getStyle("E{$row}")->getNumberFormat()->setFormatCode('"$"#,##0.00');

            $ws2->getStyle("A{$row}:E{$row}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($i % 2 === 0 ? 'FFF8F9FA' : 'FFFFFFFF');
        }

        // Totales hoja 2
        $lastRow2 = count($ordenes) + 2;
        $ws2->setCellValue("D{$lastRow2}", 'TOTAL');
        $ws2->getStyle("D{$lastRow2}")->getFont()->setBold(true);
        $ws2->setCellValue("E{$lastRow2}", "=SUM(E2:E" . ($lastRow2 - 1) . ")");
        $ws2->getStyle("E{$lastRow2}")->getFont()->setBold(true);
        $ws2->getStyle("E{$lastRow2}")->getNumberFormat()->setFormatCode('"$"#,##0.00');

        // Anchos hoja 2
        foreach (['A'=>18,'B'=>12,'C'=>20,'D'=>10,'E'=>14] as $col => $width) {
            $ws2->getColumnDimension($col)->setWidth($width);
        }

        $wb->setActiveSheetIndex(0);

        $filename = 'reporte_' . $desde->format('Ymd') . '_' . $hasta->format('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($wb, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}