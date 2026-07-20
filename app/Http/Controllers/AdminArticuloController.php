<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArticuloController extends Controller
{
    public function index(Request $request)
    {
        $query = Articulo::query();

        // Búsqueda simple
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('FOLIO', 'like', "%$q%")
                    ->orWhere('SERIE', 'like', "%$q%")
                    ->orWhere('NOMBRE', 'like', "%$q%");
            });
        }

        // Búsqueda avanzada
        if (!$request->filled('q')) {
            if ($request->filled('folio')) {
                $query->where('FOLIO', 'like', '%' . $request->folio . '%');
            }
            if ($request->filled('serie')) {
                $query->where('SERIE', 'like', '%' . $request->serie . '%');
            }
            if ($request->filled('tipo')) {
                $query->where('TIPO', $request->tipo);
            }
            if ($request->filled('formato')) {
                $query->where('FORMATO', 'like', '%' . $request->formato . '%');
            }
        }

        // Siempre ordenar por FOLIO y luego por SERIE_NUMERICO para agrupar bien
        $query->orderBy('FOLIO', 'asc')->orderBy('SERIE_NUMERICO', 'asc');

        // ── VISTA DETALLADA: todos los folios individuales, sin agrupar ──
        if ($request->get('vista') === 'detalle') {
            $paginator = $query->paginate(30)->withQueryString();
            return view('admin.articulos.index', ['paginator' => $paginator, 'vistaDetalle' => true]);
        }

        // Traemos todos los que coincidan (sin paginar individualmente)
        // y agrupamos en PHP por FOLIO
        $todosLosArticulos = $query->get();

        // Agrupar por FOLIO
        $grupos = $todosLosArticulos->groupBy('FOLIO')->map(function ($items) {
            $primero    = $items->first();
            $tieneSerie = $primero->tieneSerie();

            // Calcular rangos de series
            $rangos = [];
            if ($tieneSerie) {
                $rangos = $this->calcularRangos($items);
            }

            $almacen     = $items->sum('CANTIDAD_ALMACEN');
            $solicitudes = $items->sum('CANTIDAD_SOLICITUDES');
            $destruccion = $items->sum('CANTIDAD_DESTRUCCION');
            $perdidos    = $items->sum('CANTIDAD_PERDIDOS');

            return [
                'folio'                => $primero->FOLIO,
                'nombre'               => $primero->NOMBRE,
                'formato'              => $primero->FORMATO,
                'tipo'                 => $primero->TIPO,
                'tiene_serie'          => $tieneSerie,
                'total'                => $items->count(),
                'cantidad_almacen'     => $almacen,
                'cantidad_solicitudes' => $solicitudes,
                'cantidad_destruccion' => $destruccion,
                'cantidad_perdidos'    => $perdidos,
                'estado'               => Articulo::estadoBadge($almacen, $solicitudes, $destruccion, $perdidos),
                'rangos'               => $rangos,
                'items'                => $items,
                'es_deletable_lote'    => $items->every(fn($a) => $a->esDeletable()),
                'ids_deletables'       => $items->filter(fn($a) => $a->esDeletable())->pluck('ID_ARTICULO')->toArray(),
            ];
        });

        // Paginación manual sobre los grupos
        $porPagina  = 20;
        $paginaActual = $request->get('page', 1);
        $total      = $grupos->count();
        $gruposPaginados = $grupos->slice(($paginaActual - 1) * $porPagina, $porPagina);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $gruposPaginados,
            $total,
            $porPagina,
            $paginaActual,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.articulos.index', ['paginator' => $paginator, 'vistaDetalle' => false]);
    }
    
    /**
     * Detecta bloques de prefijo y calcula rangos numéricos dentro de cada uno.
     * Ejemplo: [A0001, A0002, A0003, B0001, B0002] → ["A0001–A0003", "B0001–B0002"]
     */
    private function calcularRangos($items): array
    {
        // Agrupar por prefijo (parte no numérica inicial de la serie)
        $porPrefijo = [];
        foreach ($items as $item) {
            // Separar prefijo de la parte numérica al final
            if (preg_match('/^(.*?)(\d+)$/', $item->SERIE, $m)) {
                $prefijo  = $m[1];
                $numerico = (int) $m[2];
                $porPrefijo[$prefijo][] = ['n' => $numerico, 'serie' => $item->SERIE];
            } else {
                // Sin patrón numérico al final: tratarlo como grupo propio
                $porPrefijo[$item->SERIE][] = ['n' => 0, 'serie' => $item->SERIE];
            }
        }

        $rangos = [];
        foreach ($porPrefijo as $prefijo => $lista) {
            usort($lista, fn($a, $b) => $a['n'] <=> $b['n']);
            $grupos = [];
            $inicio = $lista[0];
            $prev   = $lista[0];

            for ($i = 1; $i < count($lista); $i++) {
                $curr = $lista[$i];
                if ($curr['n'] === $prev['n'] + 1) {
                    $prev = $curr;
                } else {
                    $grupos[] = $inicio['serie'] === $prev['serie']
                        ? $inicio['serie']
                        : "{$inicio['serie']}–{$prev['serie']}";
                    $inicio = $curr;
                    $prev   = $curr;
                }
            }
            $grupos[] = $inicio['serie'] === $prev['serie']
                ? $inicio['serie']
                : "{$inicio['serie']}–{$prev['serie']}";

            $rangos = array_merge($rangos, $grupos);
        }

        return $rangos;
    }

    // Paso 1: buscar folio o serie
    public function buscar(Request $request)
    {
        $request->validate([
            'busqueda' => 'required|string|max:255',
            'tipo_busqueda' => 'required|in:folio,serie',
        ]);

        $busqueda = strtoupper(trim($request->busqueda));
        $resultado = [];

        if ($request->tipo_busqueda === 'serie') {
            $articulo = Articulo::where('SERIE', $busqueda)->first();

            if ($articulo) {
                return response()->json([
                    'tipo' => 'serie_existe',
                    'articulo' => $articulo,
                ]);
            }

            return response()->json([
                'tipo' => 'serie_nueva',
                'serie' => $busqueda,
            ]);
        }

        // Búsqueda por folio
        $articulos = Articulo::where('FOLIO', $busqueda)->get();

        if ($articulos->isEmpty()) {
            return response()->json([
                'tipo' => 'folio_nuevo',
                'folio' => $busqueda,
            ]);
        }

        $tieneSeries = $articulos->first()->tieneSerie();

        return response()->json([
            'tipo'       => $tieneSeries ? 'folio_con_series' : 'folio_granel',
            'folio'      => $busqueda,
            'articulos'  => $articulos,
            'nombre'     => $articulos->first()->NOMBRE,
            'descripcion'=> $articulos->first()->DESCRIPCION,
            'formato'    => $articulos->first()->FORMATO,
        ]);
    }

    // Paso 2a: guardar artículo individual (con o sin serie)
    public function store(Request $request)
    {
        $request->validate([
            'FOLIO'          => 'nullable|string|max:255',
            'NOMBRE'         => 'required|string|max:255',
            'TIPO'           => 'required|in:fisico,digital',
            'SERIE'          => 'nullable|string|max:255',
            'DESCRIPCION'    => 'nullable|string',
            'FORMATO'        => 'nullable|string|max:255',
            'COSTO_UNITARIO' => 'nullable|numeric|min:0',
            'PRECIO_VENTA'   => 'nullable|numeric|min:0',
            'CANTIDAD_ALMACEN' => 'nullable|integer|min:1',
        ]);

        $serieData = null;

        if ($request->filled('SERIE')) {
            $serieData = $this->parsearSerie($request->SERIE);

            if (!$serieData) {
                return response()->json([
                    'success' => false,
                    'message' => 'El folio no tiene el formato esperado: S seguido de 9 dígitos (ej. S451232154), con guión y dígito extra opcional.',
                ], 422);
            }

            if (Articulo::where('SERIE', $serieData['serie'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "El folio {$serieData['serie']} ya existe.",
                ], 422);
            }
        }

        $idItem = $request->filled('FOLIO') ? strtoupper($request->FOLIO) : $this->generarFolioAleatorio();

        Articulo::create([
            'FOLIO'            => $idItem,
            'SERIE'            => $serieData['serie'] ?? '',
            'SERIE_NUMERICO'   => $serieData['numerico'] ?? '',
            'NOMBRE'           => $request->NOMBRE,
            'DESCRIPCION'      => $request->DESCRIPCION ?? '',
            'FORMATO'          => strtoupper($request->FORMATO ?? ''),
            'COSTO_UNITARIO'   => $request->COSTO_UNITARIO ?? 0,
            'PRECIO_VENTA'     => $request->PRECIO_VENTA ?? 0,
            'CANTIDAD_ALMACEN' => $serieData ? 1 : ($request->CANTIDAD_ALMACEN ?? 1),
            'CANTIDAD_SOLICITUDES' => 0,
            'CANTIDAD_DESTRUCCION' => 0,
            'CANTIDAD_PERDIDOS'    => 0,
            'UBICACION_UNICA'  => 'almacen',
            'TIPO'             => $request->TIPO,
        ]);

        return response()->json(['success' => true, 'id_item' => $idItem]);
    }

    // Paso 2b: guardar lote de series
    public function storeRango(Request $request)
    {
        $request->validate([
            'FOLIO'          => 'nullable|string|max:255',
            'NOMBRE'         => 'required|string|max:255',
            'TIPO'           => 'required|in:fisico,digital',
            'DESCRIPCION'    => 'nullable|string',
            'FORMATO'        => 'nullable|string|max:255',
            'COSTO_UNITARIO' => 'nullable|numeric|min:0',
            'PRECIO_VENTA'   => 'nullable|numeric|min:0',
            'SERIE_INICIAL'  => 'required|string|max:255',
            'SERIE_FINAL'    => 'required|string|max:255',
        ]);

        $inicial = $this->parsearSerie($request->SERIE_INICIAL);
        $final   = $this->parsearSerie($request->SERIE_FINAL);

        if (!$inicial || !$final) {
            return response()->json([
                'success' => false,
                'message' => 'El folio inicial y final deben tener el formato S + 9 dígitos (ej. S451232154).',
            ], 422);
        }

        $numInicial = (int) $inicial['numerico'];
        $numFinal   = (int) $final['numerico'];

        if ($numFinal < $numInicial) {
            return response()->json(['success' => false, 'message' => 'El folio final debe ser mayor o igual al inicial.'], 422);
        }

        if (($numFinal - $numInicial) > 5000) {
            return response()->json(['success' => false, 'message' => 'El rango es demasiado grande (máximo 5000 folios por alta).'], 422);
        }

        $idItem     = $request->filled('FOLIO') ? strtoupper($request->FOLIO) : $this->generarFolioAleatorio();
        $agregados  = [];
        $existentes = [];

        for ($n = $numInicial; $n <= $numFinal; $n++) {
            $numerico = str_pad((string) $n, 9, '0', STR_PAD_LEFT);
            $serie    = 'S' . $numerico;

            if (Articulo::where('SERIE', $serie)->exists()) {
                $existentes[] = $serie;
                continue;
            }

            Articulo::create([
                'FOLIO'                => $idItem,
                'SERIE'                => $serie,
                'SERIE_NUMERICO'       => $numerico,
                'NOMBRE'               => $request->NOMBRE,
                'DESCRIPCION'          => $request->DESCRIPCION ?? '',
                'FORMATO'              => strtoupper($request->FORMATO ?? ''),
                'COSTO_UNITARIO'       => $request->COSTO_UNITARIO ?? 0,
                'PRECIO_VENTA'         => $request->PRECIO_VENTA ?? 0,
                'CANTIDAD_ALMACEN'     => 1,
                'CANTIDAD_SOLICITUDES' => 0,
                'CANTIDAD_DESTRUCCION' => 0,
                'CANTIDAD_PERDIDOS'    => 0,
                'UBICACION_UNICA'      => 'almacen',
                'TIPO'                 => $request->TIPO,
            ]);

            $agregados[] = $serie;
        }

        return response()->json([
            'success'    => true,
            'id_item'    => $idItem,
            'agregados'  => $agregados,
            'existentes' => $existentes,
        ]);
    }

    // Edición inline
    public function updateInline(Request $request, Articulo $articulo)
    {
        $campo = $request->campo;
        $camposPermitidos = ['FOLIO', 'SERIE', 'SERIE_NUMERICO', 'NOMBRE', 'DESCRIPCION', 'FORMATO', 'COSTO_UNITARIO', 'PRECIO_VENTA', 'TIPO', 'UBICACION_UNICA'];

        if (!in_array($campo, $camposPermitidos)) {
            return response()->json(['error' => 'Campo no editable.'], 422);
        }

        if ($campo === 'SERIE' && !empty($request->valor)) {
            $existe = Articulo::where('SERIE', strtoupper($request->valor))
                ->where('ID_ARTICULO', '!=', $articulo->ID_ARTICULO)
                ->exists();

            if ($existe) {
                return response()->json(['error' => 'Este número de serie ya existe.'], 422);
            }
        }

        $valor = in_array($campo, ['FOLIO', 'SERIE', 'FORMATO'])
            ? strtoupper($request->valor)
            : $request->valor;

        $articulo->update([$campo => $valor]);

        return response()->json(['success' => true, 'valor' => $articulo->$campo]);
    }

    public function show(Articulo $articulo)
    {
        $articulo->load([
            'solicitudesArticulos.solicitud.empresa',
            'solicitudesArticulos.solicitud.sede',
            'ordenesArticulos.orden',
        ]);

        // Cajas de destrucción relacionadas
        $destruccion = \App\Models\SolicitudArticulo::where('ID_ARTICULO', $articulo->ID_ARTICULO)
            ->where('CANTIDAD_A_DESTRUCCION', '>', 0)
            ->with('solicitud.empresa')
            ->get();

        return view('admin.articulos.show', compact('articulo', 'destruccion'));
    }

    public function edit(Articulo $articulo)
    {
        return view('admin.articulos.edit', compact('articulo'));
    }

    public function update(Request $request, Articulo $articulo)
    {
        $request->validate([
            'FOLIO'          => 'required|string|max:255',
            'NOMBRE'         => 'required|string|max:255',
            'DESCRIPCION'    => 'nullable|string',
            'FORMATO'        => 'nullable|string|max:255',
            'TIPO'           => 'required|in:fisico,digital',
            'COSTO_UNITARIO' => 'nullable|numeric|min:0',
            'PRECIO_VENTA'   => 'nullable|numeric|min:0',
            'SERIE'          => 'nullable|string|max:255',
            'SERIE_NUMERICO' => 'nullable|string|max:255',
        ]);

        $articulo->update([
            'FOLIO'          => strtoupper($request->FOLIO),
            'NOMBRE'         => $request->NOMBRE,
            'DESCRIPCION'    => $request->DESCRIPCION ?? '',
            'FORMATO'        => strtoupper($request->FORMATO ?? ''),
            'TIPO'           => $request->TIPO,
            'COSTO_UNITARIO' => $request->COSTO_UNITARIO ?? 0,
            'PRECIO_VENTA'   => $request->PRECIO_VENTA ?? 0,
            'SERIE'          => strtoupper($request->SERIE ?? ''),
            'SERIE_NUMERICO' => $request->SERIE_NUMERICO ?? '',
        ]);

        return redirect()->route('admin.articulos.show', $articulo)
            ->with('success', 'Artículo actualizado correctamente.');
    }

    public function agregarAlmacen(Request $request, Articulo $articulo)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $articulo->increment('CANTIDAD_ALMACEN', $request->cantidad);

        return response()->json(['success' => true, 'nueva_cantidad' => $articulo->fresh()->CANTIDAD_ALMACEN]);
    }

    public function destroy(Articulo $articulo)
    {
        if (!$articulo->esDeletable()) {
            return back()->with('error', 'No se puede eliminar: el artículo tiene relaciones activas.');
        }

        $articulo->delete();

        return redirect()->route('admin.articulos.index')
            ->with('success', 'Artículo eliminado correctamente.');
    }

    public function destroyLote(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $eliminados = 0;
        foreach ($request->ids as $id) {
            $articulo = Articulo::find($id);
            if ($articulo && $articulo->esDeletable()) {
                $articulo->delete();
                $eliminados++;
            }
        }

        return response()->json(['success' => true, 'eliminados' => $eliminados]);
    }

    public function exportar()
    {
        $articulos = Articulo::orderBy('FOLIO')->orderBy('SERIE_NUMERICO')->get();

        $destruccion = \App\Models\SolicitudArticulo::where('CANTIDAD_A_DESTRUCCION', '>', 0)
            ->with('solicitud.empresa')
            ->orderBy('UBICACION_DESTRUCCION')
            ->orderBy('FECHA_RETORNO', 'desc')
            ->get();

        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // ── HOJA 1: Artículos ──
        $ws1 = $wb->getActiveSheet();
        $ws1->setTitle('Inventario');

        $headers1 = ['ID Item', 'Folio', 'Folio (núm.)', 'Nombre', 'Descripción', 'Formato', 'Tipo', 'Costo Unit.', 'Precio Venta', 'Almacén', 'Solicitudes', 'Destrucción', 'Perdidos', 'Ubicación'];
        foreach ($headers1 as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $ws1->setCellValue($cell, $header);
            $ws1->getStyle($cell)->getFont()->setBold(true)->setName('Arial');
            $ws1->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E3A5F');
            $ws1->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
            $ws1->getStyle($cell)->getAlignment()->setHorizontal('center');
        }

        foreach ($articulos as $i => $a) {
            $row = $i + 2;
            $ws1->setCellValue("A{$row}", $a->FOLIO);
            $ws1->setCellValue("B{$row}", $a->SERIE ?: '—');
            $ws1->setCellValue("C{$row}", $a->SERIE_NUMERICO ?: '—');
            $ws1->setCellValue("D{$row}", $a->NOMBRE);
            $ws1->setCellValue("E{$row}", $a->DESCRIPCION ?: '—');
            $ws1->setCellValue("F{$row}", $a->FORMATO ?: '—');
            $ws1->setCellValue("G{$row}", $a->TIPO);
            $ws1->setCellValue("H{$row}", $a->COSTO_UNITARIO);
            $ws1->setCellValue("I{$row}", $a->PRECIO_VENTA);
            $ws1->setCellValue("J{$row}", $a->CANTIDAD_ALMACEN);
            $ws1->setCellValue("K{$row}", $a->CANTIDAD_SOLICITUDES);
            $ws1->setCellValue("L{$row}", $a->CANTIDAD_DESTRUCCION);
            $ws1->setCellValue("M{$row}", $a->CANTIDAD_PERDIDOS);
            $ws1->setCellValue("N{$row}", $a->UBICACION_UNICA);

            $ws1->getStyle("H{$row}:I{$row}")->getNumberFormat()->setFormatCode('"$"#,##0.00');

            // Zebra
            if ($i % 2 === 0) {
                $ws1->getStyle("A{$row}:N{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8F9FA');
            }

            // Resaltar si hay perdidos
            if ($a->CANTIDAD_PERDIDOS > 0) {
                $ws1->getStyle("M{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFC107');
            }
        }

        // Totales hoja 1
        $last1 = count($articulos) + 2;
        $ws1->setCellValue("I{$last1}", 'TOTALES');
        $ws1->getStyle("I{$last1}")->getFont()->setBold(true);
        foreach (['J', 'K', 'L', 'M'] as $col) {
            $ws1->setCellValue("{$col}{$last1}", "=SUM({$col}2:{$col}" . ($last1 - 1) . ")");
            $ws1->getStyle("{$col}{$last1}")->getFont()->setBold(true);
        }

        // Anchos hoja 1
        foreach (['A'=>14,'B'=>20,'C'=>12,'D'=>30,'E'=>30,'F'=>14,'G'=>10,'H'=>12,'I'=>12,'J'=>10,'K'=>12,'L'=>12,'M'=>10,'N'=>16] as $col => $w) {
            $ws1->getColumnDimension($col)->setWidth($w);
        }

        // ── HOJA 2: Cajas de Destrucción ──
        $ws2 = $wb->createSheet();
        $ws2->setTitle('Destrucción');

        $headers2 = ['Caja', 'ID Item', 'Folio', 'Nombre', 'Formato', 'Cantidad', 'Solicitud #', 'Empresa', 'Fecha retorno'];
        foreach ($headers2 as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $ws2->setCellValue($cell, $header);
            $ws2->getStyle($cell)->getFont()->setBold(true)->setName('Arial');
            $ws2->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFB45309');
            $ws2->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
            $ws2->getStyle($cell)->getAlignment()->setHorizontal('center');
        }

        foreach ($destruccion as $i => $d) {
            $row = $i + 2;
            $ws2->setCellValue("A{$row}", $d->UBICACION_DESTRUCCION ?: '—');
            $ws2->setCellValue("B{$row}", $d->FOLIO);
            $ws2->setCellValue("C{$row}", $d->SERIE ?: '—');
            $ws2->setCellValue("D{$row}", $d->NOMBRE);
            $ws2->setCellValue("E{$row}", $d->FORMATO ?: '—');
            $ws2->setCellValue("F{$row}", $d->CANTIDAD_A_DESTRUCCION);
            $ws2->setCellValue("G{$row}", $d->ID_SOLICITUD);
            $ws2->setCellValue("H{$row}", $d->solicitud?->empresa?->nombre ?? '—');
            $ws2->setCellValue("I{$row}", $d->FECHA_RETORNO ? \Carbon\Carbon::parse($d->FECHA_RETORNO)->format('d/m/Y') : '—');

            if ($i % 2 === 0) {
                $ws2->getStyle("A{$row}:I{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFF8F0');
            }
        }

        // Total hoja 2
        $last2 = count($destruccion) + 2;
        $ws2->setCellValue("E{$last2}", 'TOTAL');
        $ws2->getStyle("E{$last2}")->getFont()->setBold(true);
        $ws2->setCellValue("F{$last2}", "=SUM(F2:F" . ($last2 - 1) . ")");
        $ws2->getStyle("F{$last2}")->getFont()->setBold(true);

        // Anchos hoja 2
        foreach (['A'=>14,'B'=>14,'C'=>20,'D'=>30,'E'=>14,'F'=>10,'G'=>12,'H'=>28,'I'=>14] as $col => $w) {
            $ws2->getColumnDimension($col)->setWidth($w);
        }

        $wb->setActiveSheetIndex(0);

        $filename = 'inventario_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($wb, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function generarFolioAleatorio(): string
    {
        do {
            $folio = 'AUTO-' . strtoupper(Str::random(6));
        } while (Articulo::where('FOLIO', $folio)->exists());

        return $folio;
    }

    /**
     * Valida y normaliza un folio (antes "serie"): S + 9 dígitos, con guión y dígito extra opcional.
     * Ej: "S451232154" o "S451232154-8" → ambos válidos.
     */
    private function parsearSerie(string $serie): ?array
    {
        $serie = strtoupper(trim($serie));
        if (preg_match('/^S(\d{9})(?:-\d+)?$/', $serie, $m)) {
            return ['serie' => $serie, 'numerico' => $m[1]];
        }
        return null;
    }
}