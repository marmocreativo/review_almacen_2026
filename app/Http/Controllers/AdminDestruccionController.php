<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\SolicitudArticulo;
use Illuminate\Http\Request;

class AdminDestruccionController extends Controller
{
    public function index()
    {
        $cajas = Caja::withCount('articulos')
            ->withSum('articulos', 'CANTIDAD_A_DESTRUCCION')
            ->orderBy('NUMERO')
            ->get();

        return view('admin.destruccion.index', compact('cajas'));
    }

    public function store()
    {
        $ultimoNumero = Caja::max('NUMERO') ?? 0;
        $nuevoNumero  = $ultimoNumero + 1;

        Caja::create([
            'NOMBRE' => "Caja {$nuevoNumero}",
            'NUMERO' => $nuevoNumero,
        ]);

        return back()->with('success', "Caja {$nuevoNumero} creada correctamente.");
    }

    public function contenidoCaja(Request $request, Caja $caja)
    {
        $query = $caja->articulos()->where('CANTIDAD_A_DESTRUCCION', '>', 0);

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where('NOMBRE', 'like', "%$b%");
        }
        if ($request->filled('folio')) {
            $query->where('FOLIO', $request->folio);
        }
        if ($request->filled('serie')) {
            $query->where('SERIE', 'like', '%' . $request->serie . '%');
        }
        if ($request->filled('formato')) {
            $query->where('FORMATO', $request->formato);
        }

        $articulos = $query->orderBy('FECHA_RETORNO', 'desc')->paginate(20)->withQueryString();

        return view('admin.destruccion.caja', compact('articulos', 'caja'));
    }

    public function cerrar(Caja $caja)
    {
        if ($caja->estaCerrada()) {
            return back()->with('error', 'Esta caja ya está cerrada.');
        }

        $caja->update(['FECHA_CIERRE' => now()]);

        return back()->with('success', "{$caja->NOMBRE} cerrada correctamente.");
    }

    public function marcarDestruida(Caja $caja)
    {
        if (!$caja->estaCerrada()) {
            return back()->with('error', 'Solo se pueden marcar como destruidas las cajas cerradas.');
        }

        $caja->update(['FECHA_DESTRUIDA' => now()]);

        return back()->with('success', "{$caja->NOMBRE} marcada como destruida.");
    }
    public function exportar()
    {
        $cajas = Caja::withCount('articulos')
            ->withSum('articulos', 'CANTIDAD_A_DESTRUCCION')
            ->orderBy('NUMERO')
            ->get();

        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet();
        $ws->setTitle('Destrucción');

        $headers = ['Caja', 'Artículos', 'Cantidad total', 'Estado', 'Fecha cierre', 'Fecha destruida'];
        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $ws->setCellValue($cell, $header);
            $ws->getStyle($cell)->getFont()->setBold(true);
            $ws->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFB45309');
            $ws->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
            $ws->getStyle($cell)->getAlignment()->setHorizontal('center');
        }

        foreach ($cajas as $i => $caja) {
            $row = $i + 2;
            $estado = $caja->estaDestruida() ? 'Destruida' : ($caja->estaCerrada() ? 'Cerrada' : 'Abierta');

            $ws->setCellValue("A{$row}", $caja->NOMBRE);
            $ws->setCellValue("B{$row}", $caja->articulos_count);
            $ws->setCellValue("C{$row}", $caja->articulos_sum_CANTIDAD_A_DESTRUCCION ?? 0);
            $ws->setCellValue("D{$row}", $estado);
            $ws->setCellValue("E{$row}", $caja->FECHA_CIERRE?->format('d/m/Y H:i') ?? '—');
            $ws->setCellValue("F{$row}", $caja->FECHA_DESTRUIDA?->format('d/m/Y H:i') ?? '—');

            if ($i % 2 === 0) {
                $ws->getStyle("A{$row}:F{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFF8F0');
            }
        }

        foreach (['A'=>16,'B'=>12,'C'=>14,'D'=>14,'E'=>18,'F'=>18] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="destruccion_' . now()->format('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($wb, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}