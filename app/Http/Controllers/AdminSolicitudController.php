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
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSolicitudController extends Controller
{
    // ── INDEX ──
    public function index(Request $request)
    {
        $query = Solicitud::with(['empresa', 'sede'])
            ->withCount('examenes')
            ->withCount('articulos');

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->whereHas('empresa', fn($q) => $q->where('nombre', 'like', "%$b%"))
                  ->orWhereHas('sede', fn($q) => $q->where('nombre', 'like', "%$b%"));
        }

        if ($request->filled('estado')) {
            $query->where('ESTADO_SOLICITUD', $request->estado);
        }

        $solicitudes = $query->orderBy('FECHA_SOLICITUD', 'desc')->paginate(15)->withQueryString();

        return view('admin.solicitudes.index', compact('solicitudes'));
    }

    // ── CREATE ──
    public function create()
    {
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre')->get();
        return view('admin.solicitudes.create', compact('empresas'));
    }

    // ── STORE ──
    public function store(Request $request)
    {
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

        $solicitud = Solicitud::create([
            'ID_EMPRESA'              => $request->ID_EMPRESA,
            'ID_SEDE'                 => $request->ID_SEDE,
            'ID_CONTACTO'             => $request->ID_CONTACTO,
            'RESPONSABLE_NOMBRE'      => $request->RESPONSABLE_NOMBRE,
            'RESPONSABLE_CORREO'      => $request->RESPONSABLE_CORREO ?? '',
            'RESPONSABLE_TELEFONO'    => $request->RESPONSABLE_TELEFONO ?? '',
            'RESPONSABLE_CELULAR'     => $request->RESPONSABLE_CELULAR ?? '',
            'DIRECCION_ENVIO'         => $request->DIRECCION_ENVIO ?? '',
            'SESIONES_SIMULTANEAS'    => $request->SESIONES_SIMULTANEAS,
            'HORARIO_DE_ATENCION'     => $request->HORARIO_DE_ATENCION ?? '',
            'OBSERVACIONES'           => $request->OBSERVACIONES ?? '',
            'CANTIDAD_EXAMENES'       => 0,
            'CANTIDAD_EXAMENES_APLICADOS' => 0,
            'ESTADO_SOLICITUD'        => 'pendiente',
            'ESTADO_FACTURA'          => 'pendiente',
            'FECHA_SOLICITUD'         => now(),
        ]);

        return redirect()->route('admin.solicitudes.edit', $solicitud->ID_SOLICITUD)
            ->with('success', 'Solicitud creada. Ahora agrega los exámenes y artículos.');
    }

    // ── EDIT ──
    public function edit(Solicitud $solicitud)
    {
        $solicitud->load([
            'empresa',
            'sede',
            'contacto',
            'examenes.articulos',
        ]);
        $empresas     = Empresa::where('estado', 'activo')->orderBy('nombre')->get();
        $tipoExamenes = TipoExamen::where('estado', 'activo')->orderBy('nombre')->get();

        return view('admin.solicitudes.edit', compact('solicitud', 'empresas', 'tipoExamenes'));
    }

    // ── SHOW ──
    public function show(Solicitud $solicitud)
    {
        $solicitud->load([
            'empresa',
            'sede',
            'contacto',
            'examenes.articulos.articulo',
        ]);

        return view('admin.solicitudes.show', compact('solicitud'));
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
    public function eliminarExamen(Solicitud $solicitud, SolicitudExamen $examen)
    {
        if (!$solicitud->isPendiente()) {
            return response()->json(['error' => 'La solicitud no es editable.'], 403);
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

        return response()->json(['success' => true]);
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

    // ── AGREGAR ARTÍCULO A EXAMEN ──
    public function agregarArticulo(Request $request, Solicitud $solicitud, SolicitudExamen $examen)
    {
        if (!$solicitud->isPendiente()) {
            return response()->json(['error' => 'La solicitud no es editable.'], 403);
        }

        $request->validate([
            'tipo'     => 'required|in:series,granel,nuevo',
            'ids'      => 'required_if:tipo,series|array',
            'cantidad' => 'required_if:tipo,granel|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $solicitud, $examen) {
            if ($request->tipo === 'series') {
                $articulos = Articulo::whereIn('ID_ARTICULO', $request->ids)
                    ->where('CANTIDAD_ALMACEN', '>', 0)->get();

                foreach ($articulos as $articulo) {
                    SolicitudArticulo::create([
                        'ID_SOLICITUD'         => $solicitud->ID_SOLICITUD,
                        'ID_ARTICULO'          => $articulo->ID_ARTICULO,
                        'ID_EXAMEN'            => $examen->ID,
                        'FOLIO'                => $articulo->FOLIO,
                        'SERIE'                => $articulo->SERIE,
                        'SERIE_NUMERICO'       => $articulo->SERIE_NUMERICO,
                        'FORMATO'              => $articulo->FORMATO,
                        'NOMBRE'               => $articulo->NOMBRE,
                        'CANTIDAD_ENVIADA'     => 1,
                        'CANTIDAD_A_ALMACEN'   => 0,
                        'CANTIDAD_A_DESTRUCCION' => 0,
                        'CANTIDAD_PERDIDOS'    => 0,
                        'CANTIDAD_COBRAR'      => 0,
                        'UBICACION_DESTRUCCION' => '',
                        'RAZON_PERDIDA'        => '',
                        'PRECIO_VENTA'         => 0,
                        'NOMBRE_CANDIDATO'     => '',
                        'ESTADO'               => 'solicitud',
                    ]);
                    $articulo->decrement('CANTIDAD_ALMACEN');
                    $articulo->increment('CANTIDAD_SOLICITUDES');
                }

            } elseif ($request->tipo === 'granel') {
                $articulo = Articulo::findOrFail($request->id_articulo);
                SolicitudArticulo::create([
                    'ID_SOLICITUD'         => $solicitud->ID_SOLICITUD,
                    'ID_ARTICULO'          => $articulo->ID_ARTICULO,
                    'ID_EXAMEN'            => $examen->ID,
                    'FOLIO'                => $articulo->FOLIO,
                    'SERIE'                => '',
                    'SERIE_NUMERICO'       => '',
                    'FORMATO'              => $articulo->FORMATO,
                    'NOMBRE'               => $articulo->NOMBRE,
                    'CANTIDAD_ENVIADA'     => $request->cantidad,
                    'CANTIDAD_A_ALMACEN'   => 0,
                    'CANTIDAD_A_DESTRUCCION' => 0,
                    'CANTIDAD_PERDIDOS'    => 0,
                    'CANTIDAD_COBRAR'      => 0,
                    'UBICACION_DESTRUCCION' => '',
                    'RAZON_PERDIDA'        => '',
                    'PRECIO_VENTA'         => 0,
                    'NOMBRE_CANDIDATO'     => '',
                    'ESTADO'               => 'solicitud',
                ]);
                $articulo->decrement('CANTIDAD_ALMACEN', $request->cantidad);
                $articulo->increment('CANTIDAD_SOLICITUDES', $request->cantidad);

            } elseif ($request->tipo === 'nuevo') {
                $cantidad = $request->filled('serie') ? 1 : ($request->cantidad ?? 1);
                $articulo = Articulo::create([
                    'FOLIO'                => strtoupper($request->folio),
                    'SERIE'                => strtoupper($request->serie ?? ''),
                    'SERIE_NUMERICO'       => $request->serie_numerico ?? '',
                    'NOMBRE'               => $request->nombre,
                    'DESCRIPCION'          => $request->descripcion ?? '',
                    'FORMATO'              => strtoupper($request->formato ?? ''),
                    'COSTO_UNITARIO'       => $request->costo_unitario ?? 0,
                    'PRECIO_VENTA'         => 0,
                    'CANTIDAD_ALMACEN'     => 0,
                    'CANTIDAD_SOLICITUDES' => $cantidad,
                    'CANTIDAD_DESTRUCCION' => 0,
                    'CANTIDAD_PERDIDOS'    => 0,
                    'UBICACION_UNICA'      => 'almacen',
                    'TIPO'                 => $request->tipo_articulo ?? 'fisico',
                ]);
                SolicitudArticulo::create([
                    'ID_SOLICITUD'         => $solicitud->ID_SOLICITUD,
                    'ID_ARTICULO'          => $articulo->ID_ARTICULO,
                    'ID_EXAMEN'            => $examen->ID,
                    'FOLIO'                => $articulo->FOLIO,
                    'SERIE'                => $articulo->SERIE,
                    'SERIE_NUMERICO'       => $articulo->SERIE_NUMERICO,
                    'FORMATO'              => $articulo->FORMATO,
                    'NOMBRE'               => $articulo->NOMBRE,
                    'CANTIDAD_ENVIADA'     => $cantidad,
                    'CANTIDAD_A_ALMACEN'   => 0,
                    'CANTIDAD_A_DESTRUCCION' => 0,
                    'CANTIDAD_PERDIDOS'    => 0,
                    'CANTIDAD_COBRAR'      => 0,
                    'UBICACION_DESTRUCCION' => '',
                    'RAZON_PERDIDA'        => '',
                    'PRECIO_VENTA'         => 0,
                    'NOMBRE_CANDIDATO'     => '',
                    'ESTADO'               => 'solicitud',
                ]);
            }
        });

        return response()->json(['success' => true]);
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

    // ── RETORNAR ARTÍCULO ──
    public function retornarArticulo(Request $request, Solicitud $solicitud, SolicitudArticulo $articuloSolicitud)
    {
        if (!$solicitud->isRetornada()) {
            return response()->json(['error' => 'La solicitud debe estar en estado retornada.'], 403);
        }

        if ($articuloSolicitud->isRetornado()) {
            return response()->json(['error' => 'Este artículo ya fue retornado.'], 422);
        }

        $tieneSerie = $articuloSolicitud->tieneSerie();

        if ($tieneSerie) {
            $request->validate([
                'destino' => 'required|in:almacen,destruccion,perdido',
            ]);
            $aAlmacen      = $request->destino === 'almacen' ? 1 : 0;
            $aDestruccion  = $request->destino === 'destruccion' ? 1 : 0;
            $aPerdidos     = $request->destino === 'perdido' ? 1 : 0;
        } else {
            $request->validate([
                'cantidad_almacen'     => 'required|integer|min:0',
                'cantidad_destruccion' => 'required|integer|min:0',
                'cantidad_perdidos'    => 'required|integer|min:0',
            ]);
            $total = $request->cantidad_almacen + $request->cantidad_destruccion + $request->cantidad_perdidos;
            if ($total !== (int) $articuloSolicitud->CANTIDAD_ENVIADA) {
                return response()->json([
                    'error' => "La suma debe ser igual a la cantidad enviada ({$articuloSolicitud->CANTIDAD_ENVIADA})."
                ], 422);
            }
            $aAlmacen     = $request->cantidad_almacen;
            $aDestruccion = $request->cantidad_destruccion;
            $aPerdidos    = $request->cantidad_perdidos;
        }

        DB::transaction(function () use ($request, $solicitud, $articuloSolicitud, $aAlmacen, $aDestruccion, $aPerdidos) {
            $articuloSolicitud->update([
                'CANTIDAD_A_ALMACEN'     => $aAlmacen,
                'CANTIDAD_A_DESTRUCCION' => $aDestruccion,
                'CANTIDAD_PERDIDOS'      => $aPerdidos,
                'UBICACION_DESTRUCCION'  => $request->ubicacion_destruccion ?? '',
                'NOMBRE_CANDIDATO'       => $request->nombre_candidato ?? '',
                'RAZON_PERDIDA'          => $request->razon_perdida ?? '',
                'FECHA_RETORNO'          => now()->format('Y-m-d'),
                'ESTADO'                 => 'retornado',
            ]);

            $articulo = Articulo::find($articuloSolicitud->ID_ARTICULO);
            if ($articulo) {
                $articulo->increment('CANTIDAD_ALMACEN', $aAlmacen);
                $articulo->increment('CANTIDAD_DESTRUCCION', $aDestruccion);
                $articulo->increment('CANTIDAD_PERDIDOS', $aPerdidos);
                $total = $aAlmacen + $aDestruccion + $aPerdidos;
                $articulo->decrement('CANTIDAD_SOLICITUDES', $total);
            }
        });

        return response()->json(['success' => true]);
    }

    // ── DESTROY ──
    public function destroy(Solicitud $solicitud)
    {
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
            'FACTURA_PDF'     => 'nullable|file|mimes:pdf|max:10240',
            'FACTURA_XML'     => 'nullable|file|mimes:xml,text|max:5120',
        ]);

        $datos = [
            'IMPORTE_FACTURA' => $request->IMPORTE_FACTURA,
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
            'IMPORTE'    => 'required|numeric|min:0.01',
            'NOTAS'      => 'nullable|string|max:500',
        ]);

        if ($solicitud->ESTADO_FACTURA !== 'facturada') {
            return back()->with('error', 'Esta solicitud no tiene factura registrada.');
        }

        $solicitud->pagos()->create([
            'FECHA_PAGO' => $request->FECHA_PAGO,
            'IMPORTE'    => $request->IMPORTE,
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
            ->where('ESTADO_SOLICITUD', 'pendiente');

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
            ->where('ESTADO_SOLICITUD', 'enviada');

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
}