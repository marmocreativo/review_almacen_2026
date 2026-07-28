<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Contacto;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Solicitud;
use App\Models\SolicitudArticulo;
use App\Models\SolicitudExamen;
use App\Models\SolicitudPago;
use App\Models\TipoExamen;
use App\Models\Caja;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;

class AdminSolicitudController extends Controller
{
    public function index(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede'])
            ->withCount('examenes')
            ->withCount('articulos');

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where(function ($q) use ($b) {
                $q->whereHas('empresa', fn($qq) => $qq->where('nombre', 'like', "%$b%"))
                ->orWhereHas('sede', fn($qq) => $qq->where('nombre', 'like', "%$b%"));
            });
        }

        if ($request->filled('empresa')) {
            $query->where('ID_EMPRESA', $request->empresa);
        }

        if ($request->filled('estado')) {
            $query->where('ESTADO_SOLICITUD', $request->estado);
        }

        if ($request->filled('desde')) {
            $query->whereDate('FECHA_SOLICITUD', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('FECHA_SOLICITUD', '<=', $request->hasta);
        }

        $ordenables = [
            'FECHA_SOLICITUD' => 'FECHA_SOLICITUD',
            'EMPRESA'          => 'ID_EMPRESA',
            'ESTADO'           => 'ESTADO_SOLICITUD',
        ];
        $ordenarPor = $ordenables[$request->get('orden')] ?? 'FECHA_SOLICITUD';
        $direccion  = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($ordenarPor, $direccion);

        $solicitudes = $query->paginate(15)->withQueryString();
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre')->get();

        return view('admin.solicitudes.index', compact('solicitudes', 'empresas'));
    }

    public function exportarSolicitudes(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede'])
            ->withCount('examenes')
            ->withCount('articulos');

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where(function ($q) use ($b) {
                $q->whereHas('empresa', fn($qq) => $qq->where('nombre', 'like', "%$b%"))
                ->orWhereHas('sede', fn($qq) => $qq->where('nombre', 'like', "%$b%"));
            });
        }
        if ($request->filled('empresa')) {
            $query->where('ID_EMPRESA', $request->empresa);
        }
        if ($request->filled('estado')) {
            $query->where('ESTADO_SOLICITUD', $request->estado);
        }
        if ($request->filled('desde')) {
            $query->whereDate('FECHA_SOLICITUD', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('FECHA_SOLICITUD', '<=', $request->hasta);
        }

        $ordenables = ['FECHA_SOLICITUD' => 'FECHA_SOLICITUD', 'EMPRESA' => 'ID_EMPRESA', 'ESTADO' => 'ESTADO_SOLICITUD'];
        $ordenarPor = $ordenables[$request->get('orden')] ?? 'FECHA_SOLICITUD';
        $direccion  = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $solicitudes = $query->orderBy($ordenarPor, $direccion)->get();

        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet();
        $ws->setTitle('Solicitudes');

        $headers = ['#', 'Cliente', 'Sede', 'Responsable', 'Correo', 'Fecha', 'Exámenes', 'Artículos', 'Estado'];
        $this->estilizarEncabezado($ws, $headers);

        foreach ($solicitudes as $i => $s) {
            $row = $i + 2;
            $ws->setCellValue("A{$row}", $s->ID_SOLICITUD);
            $ws->setCellValue("B{$row}", $s->empresa?->nombre ?? '—');
            $ws->setCellValue("C{$row}", $s->sede?->nombre ?? '—');
            $ws->setCellValue("D{$row}", $s->RESPONSABLE_NOMBRE);
            $ws->setCellValue("E{$row}", $s->RESPONSABLE_CORREO);
            $ws->setCellValue("F{$row}", \Carbon\Carbon::parse($s->FECHA_SOLICITUD)->format('d/m/Y'));
            $ws->setCellValue("G{$row}", $s->examenes_count);
            $ws->setCellValue("H{$row}", $s->articulos_count);
            $ws->setCellValue("I{$row}", ucfirst($s->ESTADO_SOLICITUD));

            if ($i % 2 === 0) {
                $ws->getStyle("A{$row}:I{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8F9FA');
            }
        }

        foreach (['A'=>8,'B'=>26,'C'=>22,'D'=>24,'E'=>26,'F'=>12,'G'=>10,'H'=>10,'I'=>14] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        $this->descargarSpreadsheet($wb, 'solicitudes_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function create()
    {
        $empresas    = Empresa::where('estado', 'activo')->orderBy('nombre')->get();
        $tiposExamen = TipoExamen::where('estado', 'activo')->orderBy('nombre')->get();
        return view('admin.solicitudes.create', compact('empresas', 'tiposExamen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_EMPRESA'                => 'required|exists:empresas,id',
            'ID_SEDE'                   => 'required|exists:sedes,id',
            'ID_CONTACTO'                => 'required|exists:contactos,id',
            'RESPONSABLE_TITULO'         => 'nullable|string|max:255',
            'RESPONSABLE_NOMBRE'         => 'required|string|max:255',
            'RESPONSABLE_CORREO'         => 'nullable|email|max:255',
            'RESPONSABLE_TELEFONO'       => 'nullable|string|max:20',
            'DIRECCION_ENVIO'            => 'nullable|string',
            'SESIONES_SIMULTANEAS'       => 'required|in:si,no',
            'CANTIDAD_SIMULTANEAS'       => 'nullable|integer|min:1',
            'FECHA_PRIMERA_APLICACION'   => 'required|date',
            'CANTIDAD_USB'               => 'nullable|integer|min:0',
            'CANTIDAD_CD'                => 'nullable|integer|min:0',
            'OBSERVACIONES'              => 'nullable|string',
            'ENVIO_ZONA'                 => 'required|in:cdmx_area_metropolitana,foraneo',
            'examenes'                   => 'required|array|min:1',
            'examenes.*.tipo_examen_id'  => 'required|exists:tipo_examenes,id',
            'examenes.*.cantidad'        => 'required|integer|min:1',
        ]);

        // Validar fecha mínima según zona (10 días metropolitana, 15 foráneo)
        $diasMinimos = $request->ENVIO_ZONA === 'cdmx_area_metropolitana' ? 10 : 15;
        $fechaMinima = Carbon::now()->addDays($diasMinimos)->format('Y-m-d');

        if ($request->FECHA_PRIMERA_APLICACION < $fechaMinima) {
            return back()->withInput()->withErrors([
                'FECHA_PRIMERA_APLICACION' => "La fecha mínima de primer aplicación para esta zona es {$fechaMinima} ({$diasMinimos} días de anticipación).",
            ]);
        }

        $solicitud = DB::transaction(function () use ($request) {
            // Nombres de examen agrupados (sin duplicados) y total de candidatos
            $tipos = TipoExamen::whereIn('id', collect($request->examenes)->pluck('tipo_examen_id'))
                ->get()
                ->keyBy('id');

            $nombresExamenes = collect($request->examenes)
                ->pluck('tipo_examen_id')
                ->unique()
                ->map(fn($id) => $tipos[$id]->nombre)
                ->implode(', ');

            $totalCandidatos = collect($request->examenes)->sum('cantidad');

            $solicitud = Solicitud::create([
                'ID_EMPRESA'              => $request->ID_EMPRESA,
                'ID_SEDE'                 => $request->ID_SEDE,
                'ID_CONTACTO'             => $request->ID_CONTACTO,
                'RESPONSABLE_TITULO'      => $request->RESPONSABLE_TITULO ?? '',
                'RESPONSABLE_NOMBRE'      => $request->RESPONSABLE_NOMBRE,
                'RESPONSABLE_CORREO'      => $request->RESPONSABLE_CORREO ?? '',
                'RESPONSABLE_TELEFONO'    => $request->RESPONSABLE_TELEFONO ?? '',
                'RESPONSABLE_CELULAR'     => '',
                'DIRECCION_ENVIO'         => $request->DIRECCION_ENVIO ?? '',
                'SESIONES_SIMULTANEAS'    => $request->SESIONES_SIMULTANEAS,
                'CANTIDAD_SIMULTANEAS'    => $request->CANTIDAD_SIMULTANEAS ?? null,
                'HORARIO_DE_ATENCION'     => '',
                'FECHA_PRIMERA_APLICACION'=> $request->FECHA_PRIMERA_APLICACION,
                'CANTIDAD_USB'            => $request->CANTIDAD_USB ?? 0,
                'CANTIDAD_CD'             => $request->CANTIDAD_CD ?? 0,
                'OBSERVACIONES'           => $request->OBSERVACIONES ?? '',
                'ENVIO_ZONA'              => $request->ENVIO_ZONA,
                'ENVIO_EXAMEN'            => $nombresExamenes,
                'ENVIO_NUMERO_HOJAS'      => $totalCandidatos,
                'CANTIDAD_EXAMENES'       => 0,
                'CANTIDAD_EXAMENES_APLICADOS' => 0,
                'ESTADO_SOLICITUD'        => 'pendiente',
                'APROBACION'              => 'aprobada',
                'ESTADO_FACTURA'          => 'pendiente',
                'FECHA_SOLICITUD'         => now(),
            ]);

            foreach ($request->examenes as $fila) {
                $tipo = $tipos[$fila['tipo_examen_id']];

                SolicitudExamen::create([
                    'ID_SOLICITUD' => $solicitud->ID_SOLICITUD,
                    'EXAMEN'       => $tipo->nombre,
                    'CANTIDAD'     => $fila['cantidad'],
                    'ESTADO'       => 'pendiente',
                    'FECHA'        => $request->FECHA_PRIMERA_APLICACION,
                ]);

                $solicitud->increment('CANTIDAD_EXAMENES', $fila['cantidad']);
            }

            return $solicitud;
        });

        return redirect()->route('admin.solicitudes.envio.show', $solicitud->ID_SOLICITUD)
            ->with('success', 'Solicitud creada con sus exámenes. Ahora agrega los artículos en la pestaña Envío.');
    }

    // ── PESTAÑA: DATOS GENERALES ──
    public function showDatos(Solicitud $solicitud)
    {
        $solicitud->load(['empresa', 'sede', 'contacto']);
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre')->get();

        return view('admin.solicitudes.show-datos', compact('solicitud', 'empresas'));
    }

    public function updateDatos(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'ID_EMPRESA'                => 'required|exists:empresas,id',
            'ID_SEDE'                   => 'required|exists:sedes,id',
            'ID_CONTACTO'               => 'required|exists:contactos,id',
            'RESPONSABLE_TITULO'        => 'nullable|string|max:255',
            'RESPONSABLE_NOMBRE'        => 'required|string|max:255',
            'RESPONSABLE_CORREO'        => 'nullable|email|max:255',
            'RESPONSABLE_TELEFONO'      => 'nullable|string|max:20',
            'RESPONSABLE_CELULAR'       => 'nullable|string|max:20',
            'DIRECCION_ENVIO'           => 'nullable|string',
            'SESIONES_SIMULTANEAS'      => 'required|in:si,no',
            'CANTIDAD_SIMULTANEAS'      => 'nullable|integer|min:1',
            'FECHA_PRIMERA_APLICACION'  => 'required|date',
            'CANTIDAD_USB'              => 'nullable|integer|min:0',
            'CANTIDAD_CD'               => 'nullable|integer|min:0',
            'HORARIO_DE_ATENCION'       => 'nullable|string',
            'OBSERVACIONES'             => 'nullable|string',
            'ENVIO_ZONA'                => 'nullable|in:cdmx_area_metropolitana,foraneo',
            'APROBACION'                => 'required|in:pendiente,aprobada,cancelada',
        ]);

        $solicitud->update([
            'ID_EMPRESA'               => $request->ID_EMPRESA,
            'ID_SEDE'                  => $request->ID_SEDE,
            'ID_CONTACTO'              => $request->ID_CONTACTO,
            'RESPONSABLE_TITULO'       => $request->RESPONSABLE_TITULO ?? '',
            'RESPONSABLE_NOMBRE'       => $request->RESPONSABLE_NOMBRE,
            'RESPONSABLE_CORREO'       => $request->RESPONSABLE_CORREO ?? '',
            'RESPONSABLE_TELEFONO'     => $request->RESPONSABLE_TELEFONO ?? '',
            'RESPONSABLE_CELULAR'      => $request->RESPONSABLE_CELULAR ?? '',
            'DIRECCION_ENVIO'          => $request->DIRECCION_ENVIO ?? '',
            'SESIONES_SIMULTANEAS'     => $request->SESIONES_SIMULTANEAS,
            'CANTIDAD_SIMULTANEAS'     => $request->CANTIDAD_SIMULTANEAS ?? null,
            'FECHA_PRIMERA_APLICACION' => $request->FECHA_PRIMERA_APLICACION,
            'CANTIDAD_USB'             => $request->CANTIDAD_USB ?? 0,
            'CANTIDAD_CD'              => $request->CANTIDAD_CD ?? 0,
            'HORARIO_DE_ATENCION'      => $request->HORARIO_DE_ATENCION ?? '',
            'OBSERVACIONES'            => $request->OBSERVACIONES ?? '',
            'ENVIO_ZONA'               => $request->ENVIO_ZONA,
            'APROBACION'               => $request->APROBACION,
        ]);

        return redirect()->route('admin.solicitudes.show', $solicitud->ID_SOLICITUD)
            ->with('success', 'Datos generales actualizados correctamente.');
    }

    // ── PESTAÑA: ENVÍO ──
    public function showEnvio(Solicitud $solicitud)
    {
        $solicitud->load(['empresa', 'sede', 'contacto', 'examenes.articulos']);
        $tipoExamenes = TipoExamen::where('estado', 'activo')->orderBy('nombre')->get();

        $examenesData = $solicitud->examenes->map(function ($examen) {
            return [
                'examen'  => $examen,
                'bloques' => $this->agruparArticulosPorBloques($examen->articulos),
            ];
        });

        return view('admin.solicitudes.show-envio', compact('solicitud', 'tipoExamenes', 'examenesData'));
    }

    public function exportarEnvios(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede'])
            ->withCount('examenes')
            ->withCount('articulos')
            ->whereIn('ESTADO_SOLICITUD', ['pendiente', 'enviada']);

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->whereHas('empresa', fn($q) => $q->where('nombre', 'like', "%$b%"))
                ->orWhereHas('sede', fn($q) => $q->where('nombre', 'like', "%$b%"));
        }

        $solicitudes = $query->orderBy('FECHA_SOLICITUD', 'desc')->get();

        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet();
        $ws->setTitle('Envíos');

        $headers = ['#', 'Empresa', 'Sede', 'Responsable', 'Correo', 'Fecha solicitud', 'Exámenes', 'Artículos', 'Estado'];
        $this->estilizarEncabezado($ws, $headers);

        foreach ($solicitudes as $i => $s) {
            $row = $i + 2;
            $ws->setCellValue("A{$row}", $s->ID_SOLICITUD);
            $ws->setCellValue("B{$row}", $s->empresa?->nombre ?? '—');
            $ws->setCellValue("C{$row}", $s->sede?->nombre ?? '—');
            $ws->setCellValue("D{$row}", $s->RESPONSABLE_NOMBRE);
            $ws->setCellValue("E{$row}", $s->RESPONSABLE_CORREO);
            $ws->setCellValue("F{$row}", \Carbon\Carbon::parse($s->FECHA_SOLICITUD)->format('d/m/Y'));
            $ws->setCellValue("G{$row}", $s->examenes_count);
            $ws->setCellValue("H{$row}", $s->articulos_count);
            $ws->setCellValue("I{$row}", ucfirst($s->ESTADO_SOLICITUD));

            if ($i % 2 === 0) {
                $ws->getStyle("A{$row}:I{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFF8F0');
            }
        }

        foreach (['A'=>8,'B'=>26,'C'=>22,'D'=>24,'E'=>26,'F'=>14,'G'=>10,'H'=>10,'I'=>14] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        $this->descargarSpreadsheet($wb, 'envios_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Agrupa artículos de una solicitud (SolicitudArticulo) en bloques de series consecutivas por FOLIO.
     * Los artículos a granel (sin serie) forman un solo bloque por FOLIO.
     */
    private function agruparArticulosPorBloques($articulos)
    {
        $bloques = collect();

        foreach ($articulos->groupBy('FOLIO') as $folio => $items) {
            $primero = $items->first();

            if (!$primero->tieneSerie()) {
                $bloques->push([
                    'folio'   => $folio,
                    'nombre'  => $primero->NOMBRE,
                    'rango'   => 'Granel',
                    'count'   => $items->count(),
                    'cantidad_enviada' => $items->sum('CANTIDAD_ENVIADA'),
                    'ids'     => $items->pluck('ID')->toArray(),
                    'estado'  => $primero->ESTADO,
                    'items'   => $items->map(fn($i) => ['id' => $i->ID, 'serie' => $i->SERIE ?: '—', 'cantidad' => $i->CANTIDAD_ENVIADA])->values()->toArray(),
                ]);
                continue;
            }

            $porPrefijo = [];
            foreach ($items as $item) {
                if (preg_match('/^(.*?)(\d+)$/', $item->SERIE, $m)) {
                    $prefijo  = $m[1];
                    $numerico = (int) $m[2];
                } else {
                    $prefijo  = $item->SERIE;
                    $numerico = 0;
                }
                $porPrefijo[$prefijo][] = ['n' => $numerico, 'item' => $item];
            }

            foreach ($porPrefijo as $lista) {
                usort($lista, fn($a, $b) => $a['n'] <=> $b['n']);
                $actual = [$lista[0]];

                for ($i = 1; $i < count($lista); $i++) {
                    if ($lista[$i]['n'] === end($actual)['n'] + 1) {
                        $actual[] = $lista[$i];
                    } else {
                        $bloques->push($this->armarBloqueSolicitud($folio, $primero->NOMBRE, $actual));
                        $actual = [$lista[$i]];
                    }
                }
                $bloques->push($this->armarBloqueSolicitud($folio, $primero->NOMBRE, $actual));
            }
        }

        return $bloques;
    }

    private function armarBloqueSolicitud(string $folio, string $nombre, array $lista): array
    {
        $items   = collect($lista)->pluck('item');
        $primero = $items->first();
        $ultimo  = $items->last();

        return [
            'folio'   => $folio,
            'nombre'  => $nombre,
            'rango'   => $primero->SERIE === $ultimo->SERIE ? $primero->SERIE : "{$primero->SERIE}–{$ultimo->SERIE}",
            'count'   => $items->count(),
            'cantidad_enviada' => $items->count(),
            'ids'     => $items->pluck('ID')->toArray(),
            'estado'  => $primero->ESTADO,
            'items'   => $items->map(fn($i) => ['id' => $i->ID, 'serie' => $i->SERIE])->values()->toArray(),
        ];
    }

    // ── ELIMINAR ARTÍCULOS EN LOTE (bloque completo) ──
    public function eliminarArticulosLote(Request $request, Solicitud $solicitud)
    {
        if (!$solicitud->isPendiente()) {
            return response()->json(['error' => 'La solicitud no es editable.'], 403);
        }

        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:al_solicitudes_articulos,ID',
        ]);

        DB::transaction(function () use ($request) {
            $items = SolicitudArticulo::whereIn('ID', $request->ids)->get();

            foreach ($items as $item) {
                $articulo = Articulo::find($item->ID_ARTICULO);
                if ($articulo) {
                    $articulo->increment('CANTIDAD_ALMACEN', $item->CANTIDAD_ENVIADA);
                    $articulo->decrement('CANTIDAD_SOLICITUDES', $item->CANTIDAD_ENVIADA);
                }
            }

            SolicitudArticulo::whereIn('ID', $request->ids)->delete();
        });

        return response()->json(['success' => true]);
    }

    public function updateEnvio(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'ENVIO_ZONA'             => 'nullable|in:cdmx_area_metropolitana,foraneo',
            'ENVIO_EXAMEN'           => 'nullable|string|max:255',
            'ENVIO_VERSION'          => 'nullable|string|max:255',
            'ENVIO_NUMERO_HOJAS'     => 'nullable|integer|min:0',
            'ENVIO_PASS_USB'         => 'nullable|string|max:255',
            'ENVIO_CANTIDAD_SOBRES'  => 'nullable|integer|min:0',
            'ENVIO_FOLIOS_AUDIO'     => 'nullable|string|max:255',
            'ENVIO_FECHA_ENVIO'      => 'nullable|date',
            'ENVIO_DIAS_PERMITIDO'   => 'nullable|integer|min:0',
            'ENVIO_PAQUETERIA'       => 'nullable|string|max:255',
            'ENVIO_PAQUETERIA_GUIA'  => 'nullable|string|max:255',
            'ENVIO_PAQUETERIA_COSTO' => 'nullable|numeric|min:0',
            'ENVIO_NOTAS'            => 'nullable|string',
        ]);

        $solicitud->update([
            'ENVIO_ZONA'             => $request->ENVIO_ZONA,
            'ENVIO_EXAMEN'           => $request->ENVIO_EXAMEN ?? '',
            'ENVIO_VERSION'          => $request->ENVIO_VERSION ?? '',
            'ENVIO_NUMERO_HOJAS'     => $request->ENVIO_NUMERO_HOJAS,
            'ENVIO_PASS_USB'         => $request->ENVIO_PASS_USB ?? '',
            'ENVIO_CANTIDAD_SOBRES'  => $request->ENVIO_CANTIDAD_SOBRES,
            'ENVIO_FOLIOS_AUDIO'     => $request->ENVIO_FOLIOS_AUDIO ?? '',
            'ENVIO_FECHA_ENVIO'      => $request->ENVIO_FECHA_ENVIO,
            'ENVIO_DIAS_PERMITIDO'   => $request->ENVIO_DIAS_PERMITIDO,
            'ENVIO_PAQUETERIA'       => $request->ENVIO_PAQUETERIA ?? '',
            'ENVIO_PAQUETERIA_GUIA'  => $request->ENVIO_PAQUETERIA_GUIA ?? '',
            'ENVIO_PAQUETERIA_COSTO' => $request->ENVIO_PAQUETERIA_COSTO,
            'ENVIO_NOTAS'            => $request->ENVIO_NOTAS ?? '',
        ]);

        return redirect()->route('admin.solicitudes.envio.show', $solicitud->ID_SOLICITUD)
            ->with('success', 'Datos de envío actualizados correctamente.');
    }

    // ── PESTAÑA: DEVOLUCIÓN ──
    public function showDevolucion(Solicitud $solicitud)
    {
        $solicitud->load(['empresa', 'sede', 'examenes.articulos']);
        $cajasAbiertas = Caja::whereNull('FECHA_CIERRE')->orderBy('NUMERO')->get();

        return view('admin.solicitudes.show-devolucion', compact('solicitud', 'cajasAbiertas'));
    }

    // ── PESTAÑA: FACTURACIÓN ──
    public function showFacturacion(Solicitud $solicitud)
    {
        $solicitud->load(['empresa', 'sede', 'pagos']);

        return view('admin.solicitudes.show-facturacion', compact('solicitud'));
    }

    public function exportarFacturacion(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede'])
            ->whereIn('ESTADO_SOLICITUD', ['enviada', 'retornada']);

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->whereHas('empresa', fn($q) => $q->where('nombre', 'like', "%$b%"))
                ->orWhereHas('sede', fn($q) => $q->where('nombre', 'like', "%$b%"));
        }
        if ($request->filled('estado_factura')) {
            $query->where('ESTADO_FACTURA', $request->estado_factura);
        }

        $solicitudes = $query->orderBy('FECHA_SOLICITUD', 'desc')->get();

        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet();
        $ws->setTitle('Facturación');

        $headers = ['#', 'Empresa', 'Sede', 'Fecha solicitud', 'Importe', 'Estado factura'];
        $this->estilizarEncabezado($ws, $headers);

        foreach ($solicitudes as $i => $s) {
            $row = $i + 2;
            $ws->setCellValue("A{$row}", $s->ID_SOLICITUD);
            $ws->setCellValue("B{$row}", $s->empresa?->nombre ?? '—');
            $ws->setCellValue("C{$row}", $s->sede?->nombre ?? '—');
            $ws->setCellValue("D{$row}", \Carbon\Carbon::parse($s->FECHA_SOLICITUD)->format('d/m/Y'));
            $ws->setCellValue("E{$row}", $s->IMPORTE_FACTURA ?? 0);
            $ws->getStyle("E{$row}")->getNumberFormat()->setFormatCode('"$"#,##0.00');
            $ws->setCellValue("F{$row}", ucfirst($s->ESTADO_FACTURA));

            if ($i % 2 === 0) {
                $ws->getStyle("A{$row}:F{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8F9FA');
            }
        }

        $last = count($solicitudes) + 2;
        $ws->setCellValue("D{$last}", 'TOTAL');
        $ws->getStyle("D{$last}")->getFont()->setBold(true);
        $ws->setCellValue("E{$last}", "=SUM(E2:E" . ($last - 1) . ")");
        $ws->getStyle("E{$last}")->getFont()->setBold(true);
        $ws->getStyle("E{$last}")->getNumberFormat()->setFormatCode('"$"#,##0.00');

        foreach (['A'=>8,'B'=>26,'C'=>22,'D'=>14,'E'=>14,'F'=>16] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        $this->descargarSpreadsheet($wb, 'facturacion_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function updateFacturacion(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'ESTADO_FACTURA'            => 'required|in:pendiente,prefactura,facturada',
            'FACTURACION_FECHA'         => 'nullable|date',
            'FACTURACION_DIAS_CREDITO'  => 'nullable|integer|min:0',
            'FACTURACION_NOTAS'         => 'nullable|string',
        ]);

        $vencimiento = null;
        if ($request->filled('FACTURACION_FECHA') && $request->filled('FACTURACION_DIAS_CREDITO')) {
            $vencimiento = \Carbon\Carbon::parse($request->FACTURACION_FECHA)
                ->addDays((int) $request->FACTURACION_DIAS_CREDITO)
                ->format('Y-m-d');
        }

        $solicitud->update([
            'ESTADO_FACTURA'             => $request->ESTADO_FACTURA,
            'FACTURACION_FECHA'          => $request->FACTURACION_FECHA,
            'FACTURACION_DIAS_CREDITO'   => $request->FACTURACION_DIAS_CREDITO,
            'FACTURACION_NOTAS'          => $request->FACTURACION_NOTAS ?? '',
            'FECHA_VENCIMIENTO_COBRANZA' => $vencimiento,
        ]);

        return redirect()->route('admin.solicitudes.facturacion.show', $solicitud->ID_SOLICITUD)
            ->with('success', 'Datos de facturación actualizados correctamente.');
    }

    // ── UPDATE (datos generales) ──
    public function update(Request $request, Solicitud $solicitud)
    {
        if (!$solicitud->isPendiente()) {
            return back()->with('error', 'Solo se pueden editar solicitudes en estado pendiente.');
        }

        $request->validate([
            'ID_EMPRESA'           => 'required|exists:empresas,id',
            'ID_SEDE'              => 'required|exists:sedes,id',
            'ID_CONTACTO'          => 'required|exists:contactos,id',
            'RESPONSABLE_NOMBRE'   => 'required|string|max:255',
            'RESPONSABLE_CORREO'   => 'nullable|email|max:255',
            'RESPONSABLE_TELEFONO' => 'nullable|string|max:20',
            'RESPONSABLE_CELULAR'  => 'nullable|string|max:20',
            'DIRECCION_ENVIO'      => 'nullable|string',
            'SESIONES_SIMULTANEAS' => 'required|in:si,no',
            'HORARIO_DE_ATENCION'  => 'nullable|string',
            'OBSERVACIONES'        => 'nullable|string',
        ]);

        $solicitud->update([
            'ID_EMPRESA'           => $request->ID_EMPRESA,
            'ID_SEDE'              => $request->ID_SEDE,
            'ID_CONTACTO'          => $request->ID_CONTACTO,
            'RESPONSABLE_NOMBRE'   => $request->RESPONSABLE_NOMBRE,
            'RESPONSABLE_CORREO'   => $request->RESPONSABLE_CORREO ?? '',
            'RESPONSABLE_TELEFONO' => $request->RESPONSABLE_TELEFONO ?? '',
            'RESPONSABLE_CELULAR'  => $request->RESPONSABLE_CELULAR ?? '',
            'DIRECCION_ENVIO'      => $request->DIRECCION_ENVIO ?? '',
            'SESIONES_SIMULTANEAS' => $request->SESIONES_SIMULTANEAS,
            'HORARIO_DE_ATENCION'  => $request->HORARIO_DE_ATENCION ?? '',
            'OBSERVACIONES'        => $request->OBSERVACIONES ?? '',
        ]);

        return back()->with('success', 'Solicitud actualizada correctamente.');
    }

    // ── CAMBIAR ESTADO ──
    public function cambiarEstado(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:enviada,retornada',
        ]);

        if ($request->estado === 'enviada' && !$solicitud->isPendiente()) {
            return back()->with('error', 'Solo se puede enviar una solicitud pendiente.');
        }

        if ($request->estado === 'retornada' && !$solicitud->isEnviada()) {
            return back()->with('error', 'Solo se puede retornar una solicitud enviada.');
        }

        $solicitud->update(['ESTADO_SOLICITUD' => $request->estado]);

        if ($request->estado === 'enviada') {
            return redirect()->route('admin.solicitudes.show', $solicitud->ID_SOLICITUD)
                ->with('success', 'Solicitud marcada como enviada.');
        }

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    // ── AGREGAR EXAMEN ──
    public function agregarExamen(Request $request, Solicitud $solicitud)
    {
        if (!$solicitud->isPendiente()) {
            return response()->json(['error' => 'La solicitud no es editable.'], 403);
        }

        $request->validate([
            'tipo_examen_id' => 'required|exists:tipo_examenes,id',
            'cantidad'       => 'required|integer|min:1',
            'fecha'          => 'required|date',
        ]);

        $tipo = TipoExamen::findOrFail($request->tipo_examen_id);
        $fechaMinima = Carbon::now()->addDays($tipo->dias_anticipacion)->format('Y-m-d');

        if ($request->fecha < $fechaMinima) {
            return response()->json([
                'error' => "La fecha mínima para este examen es {$fechaMinima} ({$tipo->dias_anticipacion} días de anticipación)."
            ], 422);
        }

        if ($request->cantidad < $tipo->candidatos_minimos) {
            return response()->json([
                'error' => "La cantidad mínima de candidatos para este examen es {$tipo->candidatos_minimos}."
            ], 422);
        }

        $examen = SolicitudExamen::create([
            'ID_SOLICITUD' => $solicitud->ID_SOLICITUD,
            'EXAMEN'       => $tipo->nombre,
            'CANTIDAD'     => $request->cantidad,
            'ESTADO'       => 'pendiente',
            'FECHA'        => $request->fecha,
        ]);

        $solicitud->increment('CANTIDAD_EXAMENES', $request->cantidad);

        return response()->json(['success' => true, 'examen' => $examen->load('articulos')]);
    }

    // ── ELIMINAR EXAMEN ──
    public function eliminarExamen(Request $request, Solicitud $solicitud, SolicitudExamen $examen)
    {
        if (!$solicitud->isPendiente()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'La solicitud no es editable.'], 403);
            }
            return back()->with('error', 'La solicitud no es editable.');
        }

        DB::transaction(function () use ($solicitud, $examen) {
            // Revertir artículos del examen
            foreach ($examen->articulos as $item) {
                $articulo = Articulo::find($item->ID_ARTICULO);
                if ($articulo) {
                    $articulo->decrement('CANTIDAD_SOLICITUDES', $item->CANTIDAD_ENVIADA);
                    $articulo->increment('CANTIDAD_ALMACEN', $item->CANTIDAD_ENVIADA);
                }
            }
            $solicitud->decrement('CANTIDAD_EXAMENES', $examen->CANTIDAD);
            $examen->articulos()->delete();
            $examen->delete();
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.solicitudes.envio.show', $solicitud->ID_SOLICITUD)
            ->with('success', 'Examen eliminado correctamente.');
    }

    // ── BUSCAR ARTÍCULO PARA SOLICITUD ──
    public function buscarArticulo(Request $request)
    {
        $request->validate([
            'busqueda'      => 'required|string',
            'tipo_busqueda' => 'required|in:folio,serie',
        ]);

        $busqueda = strtoupper(trim($request->busqueda));

        if ($request->tipo_busqueda === 'serie') {
            $articulo = Articulo::where('SERIE', $busqueda)->first();

            if (!$articulo) {
                return response()->json(['tipo' => 'serie_nueva', 'serie' => $busqueda]);
            }

            return response()->json([
                'tipo'     => 'serie_encontrada',
                'articulo' => $articulo,
            ]);
        }

        $articulos = Articulo::where('FOLIO', $busqueda)->get();

        if ($articulos->isEmpty()) {
            return response()->json(['tipo' => 'folio_nuevo', 'folio' => $busqueda]);
        }

        $tieneSeries = $articulos->first()->tieneSerie();

        if ($tieneSeries) {
            $disponibles = $articulos->where('CANTIDAD_ALMACEN', '>', 0)->values();
            return response()->json([
                'tipo'        => 'folio_con_series',
                'folio'       => $busqueda,
                'disponibles' => $disponibles,
                'total'       => $articulos->count(),
                'nombre'      => $articulos->first()->NOMBRE,
                'costo'       => $articulos->first()->COSTO_UNITARIO,
            ]);
        }

        return response()->json([
            'tipo'     => 'folio_granel',
            'folio'    => $busqueda,
            'articulo' => $articulos->first(),
        ]);
    }

    // ── AGREGAR ARTÍCULOS A EXAMEN (simplificado: ID Item, folio individual o rango) ──
    public function agregarArticulo(Request $request, Solicitud $solicitud, SolicitudExamen $examen)
    {
        if (!$solicitud->isPendiente()) {
            return response()->json(['message' => 'La solicitud no es editable.'], 403);
        }

        $request->validate([
            'entradas'   => 'required|array|min:1',
            'entradas.*' => 'required|string|max:255',
        ]);

        $agregados   = [];
        $noAgregados = [];

        DB::transaction(function () use ($request, $solicitud, $examen, &$agregados, &$noAgregados) {
            foreach ($request->entradas as $entrada) {
                $this->procesarEntradaArticulo($entrada, $solicitud, $examen, $agregados, $noAgregados);
            }
        });

        if (empty($agregados) && !empty($noAgregados)) {
            return response()->json(['success' => false, 'message' => 'No se pudo agregar ningún artículo.', 'no_agregados' => $noAgregados], 422);
        }

        return response()->json(['success' => true, 'agregados' => $agregados, 'no_agregados' => $noAgregados]);
    }

    // ── REVISAR ARTÍCULOS (dry-run: no persiste nada) ──
    public function revisarArticulos(Request $request, Solicitud $solicitud, SolicitudExamen $examen)
    {
        if (!$solicitud->isPendiente()) {
            return response()->json(['message' => 'La solicitud no es editable.'], 403);
        }

        $request->validate([
            'entradas'   => 'required|array|min:1',
            'entradas.*' => 'required|string|max:255',
        ]);

        $agregados   = [];
        $noAgregados = [];

        try {
            DB::transaction(function () use ($request, $solicitud, $examen, &$agregados, &$noAgregados) {
                foreach ($request->entradas as $entrada) {
                    $this->procesarEntradaArticulo($entrada, $solicitud, $examen, $agregados, $noAgregados);
                }
                // Forzamos rollback: esto es solo una revisión, nada debe quedar guardado.
                throw new \RuntimeException('__dry_run_rollback__');
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() !== '__dry_run_rollback__') {
                throw $e;
            }
        }

        return response()->json(['agregados' => $agregados, 'no_agregados' => $noAgregados]);
    }

    private function procesarEntradaArticulo(string $entrada, Solicitud $solicitud, SolicitudExamen $examen, array &$agregados, array &$noAgregados): void
    {
        $entrada = strtoupper(trim($entrada));

        // Rango: S000000001-S000000010 (ambos lados con S + 9 dígitos)
        if (preg_match('/^S(\d{9})-S(\d{9})$/', $entrada, $m)) {
            $inicio = (int) $m[1];
            $fin    = (int) $m[2];

            if ($fin < $inicio) {
                $noAgregados[] = "{$entrada}: el folio final debe ser mayor o igual al inicial.";
                return;
            }
            if (($fin - $inicio) > 2000) {
                $noAgregados[] = "{$entrada}: rango demasiado grande (máx. 2000 folios).";
                return;
            }

            for ($n = $inicio; $n <= $fin; $n++) {
                $serie = 'S' . str_pad((string) $n, 9, '0', STR_PAD_LEFT);
                $this->agregarPorSerie($serie, $solicitud, $examen, $agregados, $noAgregados);
            }
            return;
        }

        // Folio individual: S000000001 o S000000001-8 (dígito verificador)
        if (preg_match('/^S(\d{9})(?:-\d+)?$/', $entrada)) {
            $this->agregarPorSerie($entrada, $solicitud, $examen, $agregados, $noAgregados);
            return;
        }

        // Si no matchea patrón de folio, se trata como ID Item (opcionalmente con :cantidad)
        $cantidadSolicitada = null;
        $idItemEntrada = $entrada;

        if (str_contains($entrada, ':')) {
            [$idItemEntrada, $cantidadTexto] = array_map('trim', explode(':', $entrada, 2));

            if (!ctype_digit($cantidadTexto) || (int) $cantidadTexto < 1) {
                $noAgregados[] = "{$entrada}: la cantidad indicada no es válida.";
                return;
            }
            $cantidadSolicitada = (int) $cantidadTexto;
        }

        $this->agregarPorIdItem($idItemEntrada, $solicitud, $examen, $agregados, $noAgregados, $cantidadSolicitada);
    }

    private function agregarPorSerie(string $serie, Solicitud $solicitud, SolicitudExamen $examen, array &$agregados, array &$noAgregados): void
    {
        $articulo = Articulo::where('SERIE', $serie)->first();

        if (!$articulo) {
            $noAgregados[] = "{$serie}: no existe en inventario.";
            return;
        }
        if ($solicitud->ENVIO_VERSION && $articulo->FORMATO && $articulo->FORMATO !== $solicitud->ENVIO_VERSION) {
            $noAgregados[] = "{$serie}: el formato del artículo ({$articulo->FORMATO}) no coincide con la versión de la solicitud ({$solicitud->ENVIO_VERSION}).";
            return;
        }
        if ($articulo->CANTIDAD_ALMACEN < 1) {
            $noAgregados[] = "{$serie}: sin existencia en almacén.";
            return;
        }

        SolicitudArticulo::create([
            'ID_SOLICITUD'          => $solicitud->ID_SOLICITUD,
            'ID_ARTICULO'           => $articulo->ID_ARTICULO,
            'ID_EXAMEN'             => $examen->ID,
            'FOLIO'                 => $articulo->FOLIO,
            'SERIE'                 => $articulo->SERIE,
            'SERIE_NUMERICO'        => $articulo->SERIE_NUMERICO,
            'FORMATO'               => $articulo->FORMATO,
            'NOMBRE'                => $articulo->NOMBRE,
            'CANTIDAD_ENVIADA'      => 1,
            'CANTIDAD_A_ALMACEN'    => 0,
            'CANTIDAD_A_DESTRUCCION' => 0,
            'CANTIDAD_PERDIDOS'     => 0,
            'CANTIDAD_COBRAR'       => 0,
            'UBICACION_DESTRUCCION' => '',
            'RAZON_PERDIDA'         => '',
            'PRECIO_VENTA'          => 0,
            'NOMBRE_CANDIDATO'      => '',
            'ESTADO'                => 'solicitud',
        ]);
        $articulo->decrement('CANTIDAD_ALMACEN');
        $articulo->increment('CANTIDAD_SOLICITUDES');

        $formatoTxt = $articulo->FORMATO ?: 'sin formato';
        $agregados[] = "{$serie} (formato: {$formatoTxt})";
    }

    private function agregarPorIdItem(string $idItem, Solicitud $solicitud, SolicitudExamen $examen, array &$agregados, array &$noAgregados, ?int $cantidadSolicitada = null): void
    {
        $articulos = Articulo::where('FOLIO', $idItem)->get();

        if ($articulos->isEmpty()) {
            $noAgregados[] = "{$idItem}: ID Item no existe en inventario.";
            return;
        }

        $tieneSerie = $articulos->first()->tieneSerie();

        if ($tieneSerie) {
            if ($cantidadSolicitada !== null) {
                $noAgregados[] = "{$idItem}: este ID Item maneja folios individuales, no admite cantidad.";
                return;
            }

            $disponibles = $articulos->where('CANTIDAD_ALMACEN', '>', 0);

            if ($disponibles->isEmpty()) {
                $noAgregados[] = "{$idItem}: sin folios disponibles en almacén.";
                return;
            }

            foreach ($disponibles as $articulo) {
                $this->agregarPorSerie($articulo->SERIE, $solicitud, $examen, $agregados, $noAgregados);
            }
            return;
        }

        // A granel
        $articulo = $articulos->first();

        if ($solicitud->ENVIO_VERSION && $articulo->FORMATO && $articulo->FORMATO !== $solicitud->ENVIO_VERSION) {
            $noAgregados[] = "{$idItem}: el formato del artículo ({$articulo->FORMATO}) no coincide con la versión de la solicitud ({$solicitud->ENVIO_VERSION}).";
            return;
        }

        $cantidad = $cantidadSolicitada ?? $articulo->CANTIDAD_ALMACEN;

        if ($articulo->CANTIDAD_ALMACEN < 1) {
            $noAgregados[] = "{$idItem}: sin existencia en almacén.";
            return;
        }
        if ($cantidad > $articulo->CANTIDAD_ALMACEN) {
            $noAgregados[] = "{$idItem}: solicitaste {$cantidad}, solo hay {$articulo->CANTIDAD_ALMACEN} en almacén.";
            return;
        }

        SolicitudArticulo::create([
            'ID_SOLICITUD'          => $solicitud->ID_SOLICITUD,
            'ID_ARTICULO'           => $articulo->ID_ARTICULO,
            'ID_EXAMEN'             => $examen->ID,
            'FOLIO'                 => $articulo->FOLIO,
            'SERIE'                 => '',
            'SERIE_NUMERICO'        => '',
            'FORMATO'               => $articulo->FORMATO,
            'NOMBRE'                => $articulo->NOMBRE,
            'CANTIDAD_ENVIADA'      => $cantidad,
            'CANTIDAD_A_ALMACEN'    => 0,
            'CANTIDAD_A_DESTRUCCION' => 0,
            'CANTIDAD_PERDIDOS'     => 0,
            'CANTIDAD_COBRAR'       => 0,
            'UBICACION_DESTRUCCION' => '',
            'RAZON_PERDIDA'         => '',
            'PRECIO_VENTA'          => 0,
            'NOMBRE_CANDIDATO'      => '',
            'ESTADO'                => 'solicitud',
        ]);

        $articulo->decrement('CANTIDAD_ALMACEN', $cantidad);
        $articulo->increment('CANTIDAD_SOLICITUDES', $cantidad);

        $formatoTxt = $articulo->FORMATO ?: 'sin formato';
        $agregados[] = "{$idItem} (x{$cantidad}, formato: {$formatoTxt})";
    }

    // ── ELIMINAR ARTÍCULO DE SOLICITUD ──
    public function eliminarArticulo(Solicitud $solicitud, SolicitudArticulo $articuloSolicitud)
    {
        if (!$solicitud->isPendiente()) {
            return response()->json(['error' => 'La solicitud no es editable.'], 403);
        }

        $articulo = Articulo::find($articuloSolicitud->ID_ARTICULO);
        if ($articulo) {
            $articulo->increment('CANTIDAD_ALMACEN', $articuloSolicitud->CANTIDAD_ENVIADA);
            $articulo->decrement('CANTIDAD_SOLICITUDES', $articuloSolicitud->CANTIDAD_ENVIADA);
        }

        $articuloSolicitud->delete();
        return response()->json(['success' => true]);
    }

    // ── PROCESAR DEVOLUCIÓN EN LOTE ──
    public function procesarDevolucion(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'id_caja_destino'              => 'nullable|integer|exists:al_cajas,ID',
            'reedicion'                    => 'nullable|boolean',
            'items'                        => 'required|array',
            'items.*.id'                   => 'required|integer|exists:al_solicitudes_articulos,ID',
            'items.*.estado_devolucion'    => 'required|in:aplicado,no_aplicado,danado,faltante',
            'items.*.nombre_candidato'     => 'nullable|string|max:255',
        ]);

        $esReedicion = $request->boolean('reedicion');

        // Validar que la caja elegida (si viene) siga abierta
        $cajaElegida = null;
        if ($request->filled('id_caja_destino')) {
            $cajaElegida = Caja::whereNull('FECHA_CIERRE')->find($request->id_caja_destino);
            if (!$cajaElegida) {
                return back()->with('error', 'La caja seleccionada ya no está disponible (fue cerrada). Selecciona otra.');
            }
        }

        $resumen = [];
        $cambios = []; // solo para reedición: registro de qué cambió

        DB::transaction(function () use ($request, $solicitud, $cajaElegida, $esReedicion, &$resumen, &$cambios) {
            $cajaParaDestruccion = $cajaElegida;

            foreach ($request->items as $itemData) {
                $sa = SolicitudArticulo::find($itemData['id']);

                if (!$sa || $sa->ID_SOLICITUD != $solicitud->ID_SOLICITUD) {
                    continue;
                }

                $yaEstabaRetornado = $sa->isRetornado();

                // Si ya estaba retornado y NO es una reedición explícita, se omite (comportamiento normal).
                if ($yaEstabaRetornado && !$esReedicion) {
                    continue;
                }

                $articulo = Articulo::find($sa->ID_ARTICULO);

                // Si es reedición de un artículo ya retornado, primero revertimos su efecto anterior en inventario.
                if ($yaEstabaRetornado && $esReedicion && $articulo) {
                    $articulo->decrement('CANTIDAD_ALMACEN', $sa->CANTIDAD_A_ALMACEN);
                    $articulo->decrement('CANTIDAD_DESTRUCCION', $sa->CANTIDAD_A_DESTRUCCION);
                    $articulo->decrement('CANTIDAD_PERDIDOS', $sa->CANTIDAD_PERDIDOS);
                    $articulo->increment('CANTIDAD_SOLICITUDES', $sa->CANTIDAD_ENVIADA);
                    $articulo->refresh();
                }

                $estadoAnterior = $sa->ESTADO_DEVOLUCION;
                $estado         = $itemData['estado_devolucion'];
                $cantidad       = $sa->CANTIDAD_ENVIADA;

                $aAlmacen = $aDestruccion = $aPerdidos = 0;
                $ubicacion = '';
                $idCaja = null;

                if (in_array($estado, ['aplicado', 'danado'])) {
                    $aDestruccion = $cantidad;

                    if (!$cajaParaDestruccion) {
                        $cajaParaDestruccion = $this->obtenerCajaAbierta();
                    }

                    $idCaja    = $cajaParaDestruccion->ID;
                    $ubicacion = $cajaParaDestruccion->NOMBRE;
                } elseif ($estado === 'no_aplicado') {
                    $aAlmacen = $cantidad;
                } elseif ($estado === 'faltante') {
                    $aPerdidos = $cantidad;
                }

                $sa->update([
                    'ESTADO_DEVOLUCION'      => $estado,
                    'NOMBRE_CANDIDATO'       => $itemData['nombre_candidato'] ?? $sa->NOMBRE_CANDIDATO,
                    'CANTIDAD_A_ALMACEN'     => $aAlmacen,
                    'CANTIDAD_A_DESTRUCCION' => $aDestruccion,
                    'CANTIDAD_PERDIDOS'      => $aPerdidos,
                    'UBICACION_DESTRUCCION'  => $ubicacion,
                    'ID_CAJA'                => $idCaja,
                    'FECHA_RETORNO'          => now()->format('Y-m-d'),
                    'ESTADO'                 => 'retornado',
                ]);

                if ($articulo) {
                    $articulo->increment('CANTIDAD_ALMACEN', $aAlmacen);
                    $articulo->increment('CANTIDAD_DESTRUCCION', $aDestruccion);
                    $articulo->increment('CANTIDAD_PERDIDOS', $aPerdidos);
                    $articulo->decrement('CANTIDAD_SOLICITUDES', $cantidad);
                }

                $descripcionArticulo = ($articulo?->FOLIO ?? "ID {$sa->ID_ARTICULO}")
                    . ($articulo?->SERIE ? " ({$articulo->SERIE})" : '');

                if ($yaEstabaRetornado && $esReedicion) {
                    $cambios[] = "{$descripcionArticulo}: {$estadoAnterior} → {$estado}";
                } else {
                    $resumen[] = "{$descripcionArticulo} → {$estado}";
                }
            }

            // Si todos los artículos de la solicitud ya quedaron retornados, se marca la solicitud como retornada automáticamente.
            $totalArticulos   = $solicitud->articulos()->count();
            $totalRetornados  = $solicitud->articulos()->where('ESTADO', 'retornado')->count();

            if ($totalArticulos > 0 && $totalArticulos === $totalRetornados && $solicitud->ESTADO_SOLICITUD !== 'retornada') {
                $solicitud->update(['ESTADO_SOLICITUD' => 'retornada']);
            }
        });

        if (!empty($resumen)) {
            Bitacora::registrar('solicitudes', 'procesar_devolucion',
                "Procesó devolución de " . count($resumen) . " artículo(s) de la solicitud #{$solicitud->ID_SOLICITUD}"
                    . ($solicitud->empresa ? " de {$solicitud->empresa->nombre}" : ''),
                $solicitud->ID_SOLICITUD,
                [
                    'id_solicitud' => $solicitud->ID_SOLICITUD,
                    'cliente'      => $solicitud->empresa?->nombre ?? 'Sin cliente',
                    'total'        => count($resumen),
                    'items'        => array_slice($resumen, 0, 20),
                ]
            );
        }

        if (!empty($cambios)) {
            Bitacora::registrar('solicitudes', 'reeditar_devolucion',
                "Reeditó la devolución de " . count($cambios) . " artículo(s) de la solicitud #{$solicitud->ID_SOLICITUD}"
                    . ($solicitud->empresa ? " de {$solicitud->empresa->nombre}" : ''),
                $solicitud->ID_SOLICITUD,
                [
                    'id_solicitud' => $solicitud->ID_SOLICITUD,
                    'cliente'      => $solicitud->empresa?->nombre ?? 'Sin cliente',
                    'total'        => count($cambios),
                    'cambios'      => array_slice($cambios, 0, 20),
                ]
            );
        }

        return redirect()->route('admin.solicitudes.devolucion.show', $solicitud->ID_SOLICITUD)
            ->with('success', $esReedicion ? 'Devolución reeditada correctamente.' : 'Devolución procesada correctamente.');
    }

/**
 * Devuelve una caja de destrucción abierta (FECHA_CIERRE null).
 * Si no hay ninguna abierta, crea una nueva con el siguiente número consecutivo.
 */
private function obtenerCajaAbierta(): Caja
{
    $caja = Caja::whereNull('FECHA_CIERRE')->orderBy('NUMERO')->first();

    if ($caja) {
        return $caja;
    }

    $ultimoNumero = Caja::max('NUMERO') ?? 0;
    $nuevoNumero  = $ultimoNumero + 1;

    return Caja::create([
        'NOMBRE'  => "Caja {$nuevoNumero}",
        'NUMERO'  => $nuevoNumero,
    ]);
}
    // ── DESTROY ──
    public function destroy(Solicitud $solicitud)
    {
        Bitacora::registrar('solicitudes', 'eliminar',
            "Eliminó la solicitud #{$solicitud->ID_SOLICITUD}" . ($solicitud->empresa ? " de {$solicitud->empresa->nombre}" : ''),
            $solicitud->ID_SOLICITUD,
            [
                'id_solicitud' => $solicitud->ID_SOLICITUD,
                'cliente'      => $solicitud->empresa?->nombre ?? 'Sin cliente',
                'sede'         => $solicitud->sede?->nombre,
                'estado'       => $solicitud->ESTADO_SOLICITUD,
            ]
        );

        DB::transaction(function () use ($solicitud) {
            foreach ($solicitud->articulos as $item) {
                $articulo = Articulo::find($item->ID_ARTICULO);
                if ($articulo) {
                    $articulo->increment('CANTIDAD_ALMACEN', $item->CANTIDAD_ENVIADA + $item->CANTIDAD_A_DESTRUCCION + $item->CANTIDAD_PERDIDOS);
                    $articulo->decrement('CANTIDAD_SOLICITUDES', $item->CANTIDAD_ENVIADA);
                    $articulo->decrement('CANTIDAD_DESTRUCCION', $item->CANTIDAD_A_DESTRUCCION);
                    $articulo->decrement('CANTIDAD_PERDIDOS', $item->CANTIDAD_PERDIDOS);
                }
            }
            $solicitud->articulos()->delete();
            $solicitud->examenes()->delete();
            $solicitud->delete();
        });

        return redirect()->route('admin.solicitudes.index')
            ->with('success', 'Solicitud eliminada correctamente.');
    }

    // ── AJAX: obtener sedes por empresa ──
    public function getSedes(Empresa $empresa)
    {
        return response()->json($empresa->sedes()->where('estado', 'activo')->get());
    }

    // ── AJAX: obtener contactos por sede ──
    public function getContactos(Sede $sede)
    {
        return response()->json($sede->contactos);
    }

    // ── GUARDAR FACTURA ──
    public function guardarFactura(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'IMPORTE_FACTURA' => 'required|numeric|min:0',
            'FOLIO_FACTURA'   => 'nullable|string|max:100',
            'FACTURA_PDF'     => 'nullable|file|mimes:pdf|max:10240',
            'FACTURA_XML'     => 'nullable|file|mimes:xml,text|max:5120',
        ]);

        $datos = [
            'IMPORTE_FACTURA' => $request->IMPORTE_FACTURA,
            'FOLIO_FACTURA'   => $request->FOLIO_FACTURA ?? '',
            'ESTADO_FACTURA'  => 'facturada',
        ];

        if ($request->hasFile('FACTURA_PDF')) {
            // Eliminar archivo anterior si existe
            if ($solicitud->FACTURA_PDF && Storage::disk('public')->exists($solicitud->FACTURA_PDF)) {
                Storage::disk('public')->delete($solicitud->FACTURA_PDF);
            }
            $datos['FACTURA_PDF'] = $request->file('FACTURA_PDF')
                ->store("facturas/{$solicitud->ID_SOLICITUD}", 'public');
        }

        if ($request->hasFile('FACTURA_XML')) {
            if ($solicitud->FACTURA_XML && Storage::disk('public')->exists($solicitud->FACTURA_XML)) {
                Storage::disk('public')->delete($solicitud->FACTURA_XML);
            }
            $datos['FACTURA_XML'] = $request->file('FACTURA_XML')
                ->store("facturas/{$solicitud->ID_SOLICITUD}", 'public');
        }

        $solicitud->update($datos);

        return back()->with('success', 'Factura guardada correctamente.');
    }

    // ── GENERAR PDF ──
    public function generarPdf(Solicitud $solicitud)
    {
        $solicitud->load([
            'empresa', 'sede', 'contacto',
            'examenes.articulos',
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.solicitudes.pdf', compact('solicitud'))
            ->setPaper('letter', 'portrait');

        return $pdf->download("solicitud-{$solicitud->ID_SOLICITUD}.pdf");
    }

    // ── GENERAR CARTA DE ENVÍO EN WORD ──
public function generarCartaWord(Solicitud $solicitud)
{
    $solicitud->load(['empresa', 'sede', 'contacto', 'examenes.articulos']);

    \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $phpWord->setDefaultFontName('Calibri');
    $phpWord->setDefaultFontSize(11);

    $section = $phpWord->addSection();

    $fechaBase = $solicitud->ENVIO_FECHA_ENVIO ?: $solicitud->FECHA_SOLICITUD;
    $fecha = $fechaBase ? \Carbon\Carbon::parse($fechaBase)->format('d/m/Y') : now()->format('d/m/Y');

    $nombreContacto = trim(($solicitud->contacto?->nombre ?? '') . ' ' . ($solicitud->contacto?->apellidos ?? ''));
    $nombreContacto = $nombreContacto ?: $solicitud->RESPONSABLE_NOMBRE;
    $nombreEmpresa  = $solicitud->empresa?->nombre ?: '';

    $nombreExamen = $solicitud->ENVIO_EXAMEN ?: '(sin especificar)';

    $series = $solicitud->examenes
        ->flatMap->articulos
        ->pluck('SERIE')
        ->filter()
        ->unique()
        ->values();

    $foliosMaterial = $series->isNotEmpty() ? $series->implode(', ') : '(sin folios)';
    $foliosAudio    = $solicitud->ENVIO_FOLIOS_AUDIO ?: '(sin especificar)';
    $numeroHojas    = $solicitud->ENVIO_NUMERO_HOJAS ?? '(sin especificar)';
    $diasPermitido  = $solicitud->ENVIO_DIAS_PERMITIDO ?? '(sin especificar)';

    $section->addText($fecha);
    $section->addTextBreak(1);

    $section->addText('Para: ' . $nombreContacto);
    $section->addText($nombreEmpresa);
    $section->addTextBreak(1);

    $section->addText('Por medio de la presente confirmamos el envio del material correspondiente a su solicitud de examen ' . $nombreExamen . ', con los siguientes detalles:');
    $section->addTextBreak(1);

    $section->addText('Folios de material: ' . $foliosMaterial);
    $section->addText('Folios de audio: ' . $foliosAudio);
    $section->addText('Folios de hojas de respuesta: ' . $numeroHojas);
    $section->addText('Fecha de envio: ' . $fecha);
    $section->addText('Dias permitidos de resguardo: ' . $diasPermitido);
    $section->addTextBreak(1);

    $section->addText('Le solicitamos custodiar el material y devolverlo dentro del plazo indicado. Cualquier duda quedamos a sus ordenes.');
    $section->addTextBreak(2);

    $section->addText('Atentamente,');
    $section->addText('Departamento de Operaciones');

    $filename = "carta-envio-solicitud-{$solicitud->ID_SOLICITUD}.docx";

    if (!is_dir(storage_path('app/temp'))) {
        mkdir(storage_path('app/temp'), 0755, true);
    }

    $tempPath = storage_path('app/temp/' . uniqid('carta_') . '.docx');

    $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($tempPath);

    // Descartar cualquier salida previa (BOM, espacios, warnings) que corrompa el binario
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    return response()->download($tempPath, $filename, [
        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'Content-Length'      => filesize($tempPath),
        'Cache-Control'       => 'no-store, no-cache, must-revalidate',
        'Pragma'              => 'public',
        'X-Accel-Buffering'   => 'no',
    ])->deleteFileAfterSend(true);
}

    // ── ENVIAR EMAIL ──
    public function enviarEmail(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'email_destino' => 'required|email',
        ]);

        $solicitud->load([
            'empresa', 'sede', 'contacto',
            'examenes.articulos',
        ]);

        // Generar PDF en memoria
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.solicitudes.pdf', compact('solicitud'))
            ->setPaper('letter', 'portrait');

        \Illuminate\Support\Facades\Mail::send(
            'admin.solicitudes.email',
            ['solicitud' => $solicitud],
            function ($message) use ($request, $solicitud, $pdf) {
                $message->to($request->email_destino)
                    ->subject("Solicitud #{$solicitud->ID_SOLICITUD} — {$solicitud->empresa?->nombre}")
                    ->attachData($pdf->output(), "solicitud-{$solicitud->ID_SOLICITUD}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            }
        );

        return back()->with('success', "Correo enviado a {$request->email_destino}.");
    }

    // ── COBRANZA: listado de solicitudes ya facturadas ──
    public function indexCobranza(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede', 'pagos'])
            ->where('ESTADO_FACTURA', 'facturada');

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->whereHas('empresa', fn($q) => $q->where('nombre', 'like', "%$b%"))
                  ->orWhereHas('sede', fn($q) => $q->where('nombre', 'like', "%$b%"));
        }

        $solicitudes = $query->orderBy('FECHA_SOLICITUD', 'desc')->paginate(15)->withQueryString();

        if ($request->filled('estado_cobranza')) {
            $solicitudes->setCollection(
                $solicitudes->getCollection()->filter(
                    fn($s) => $s->estadoCobranza()['label'] === ucfirst($request->estado_cobranza)
                )->values()
            );
        }

        return view('admin.solicitudes.cobranza', compact('solicitudes'));
    }

    // ── GUARDAR FECHA DE VENCIMIENTO ──
    public function guardarVencimiento(Request $request, Solicitud $solicitud)
    {
        $request->validate(['FECHA_VENCIMIENTO_COBRANZA' => 'required|date']);

        $solicitud->update(['FECHA_VENCIMIENTO_COBRANZA' => $request->FECHA_VENCIMIENTO_COBRANZA]);

        return back()->with('success', 'Fecha de vencimiento actualizada.');
    }

    // ── AGREGAR PAGO ──
    public function agregarPago(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'FECHA_PAGO' => 'required|date',
            'IMPORTE'    => 'required|numeric|min:0.01|max:' . max($solicitud->saldoPendiente(), 0),
            'FORMA_PAGO' => 'nullable|string|max:100',
            'NOTAS'      => 'nullable|string|max:500',
        ], [
            'IMPORTE.max' => 'El importe no puede exceder el saldo pendiente ($' . number_format($solicitud->saldoPendiente(), 2) . ').',
        ]);

        if ($solicitud->ESTADO_FACTURA !== 'facturada') {
            return back()->with('error', 'Esta solicitud no tiene factura registrada.');
        }

        $solicitud->pagos()->create([
            'FECHA_PAGO' => $request->FECHA_PAGO,
            'IMPORTE'    => $request->IMPORTE,
            'FORMA_PAGO' => $request->FORMA_PAGO ?? '',
            'NOTAS'      => $request->NOTAS ?? '',
        ]);

        return back()->with('success', 'Pago registrado correctamente.');
    }

    // ── ELIMINAR PAGO ──
    public function eliminarPago(Solicitud $solicitud, SolicitudPago $pago)
    {
        if ($pago->ID_SOLICITUD != $solicitud->ID_SOLICITUD) {
            abort(404);
        }

        $pago->delete();

        return back()->with('success', 'Pago eliminado correctamente.');
    }

    // ── ENVÍOS: solicitudes pendientes de armar/enviar ──
    public function indexEnvios(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede'])
            ->withCount('examenes')
            ->withCount('articulos')
            ->whereIn('ESTADO_SOLICITUD', ['pendiente', 'enviada']);

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->whereHas('empresa', fn($q) => $q->where('nombre', 'like', "%$b%"))
                  ->orWhereHas('sede', fn($q) => $q->where('nombre', 'like', "%$b%"));
        }

        $solicitudes = $query->orderBy('FECHA_SOLICITUD', 'desc')->paginate(15)->withQueryString();

        return view('admin.solicitudes.envios', compact('solicitudes'));
    }

    // ── DEVOLUCIONES: solicitudes enviadas o ya marcadas como retornadas ──
    public function indexDevoluciones(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede'])
            ->withCount('examenes')
            ->withCount('articulos')
            ->whereIn('ESTADO_SOLICITUD', ['enviada', 'retornada']);

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->whereHas('empresa', fn($q) => $q->where('nombre', 'like', "%$b%"))
                  ->orWhereHas('sede', fn($q) => $q->where('nombre', 'like', "%$b%"));
        }

        $solicitudes = $query->orderBy('FECHA_SOLICITUD', 'desc')->paginate(15)->withQueryString();

        return view('admin.solicitudes.devoluciones', compact('solicitudes'));
    }

    // ── FACTURACIÓN: solicitudes ya enviadas, pendientes o con factura cargada ──
    public function indexFacturacion(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede'])
            ->whereIn('ESTADO_SOLICITUD', ['enviada', 'retornada']);

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->whereHas('empresa', fn($q) => $q->where('nombre', 'like', "%$b%"))
                  ->orWhereHas('sede', fn($q) => $q->where('nombre', 'like', "%$b%"));
        }

        if ($request->filled('estado_factura')) {
            $query->where('ESTADO_FACTURA', $request->estado_factura);
        }

        $solicitudes = $query->orderBy('FECHA_SOLICITUD', 'desc')->paginate(15)->withQueryString();

        return view('admin.solicitudes.facturacion', compact('solicitudes'));
    }

    private function estilizarEncabezado($ws, array $headers)
    {
        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $ws->setCellValue($cell, $header);
            $ws->getStyle($cell)->getFont()->setBold(true);
            $ws->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0F448A');
            $ws->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
            $ws->getStyle($cell)->getAlignment()->setHorizontal('center');
        }
    }

    private function descargarSpreadsheet($wb, string $nombreArchivo)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($wb, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function aprobar(Solicitud $solicitud)
    {
        if ($solicitud->APROBACION === 'aprobada') {
            return back()->with('success', 'Esta solicitud ya estaba aprobada.');
        }

        $solicitud->update(['APROBACION' => 'aprobada']);

        Bitacora::registrar('solicitudes', 'aprobar_solicitud',
            "Aprobó la solicitud #{$solicitud->ID_SOLICITUD}" . ($solicitud->empresa ? " de {$solicitud->empresa->nombre}" : ''),
            $solicitud->ID_SOLICITUD,
            [
                'id_solicitud' => $solicitud->ID_SOLICITUD,
                'cliente'      => $solicitud->empresa?->nombre ?? 'Sin cliente',
            ]
        );

        return back()->with('success', "Solicitud #{$solicitud->ID_SOLICITUD} aprobada correctamente.");
    }
}