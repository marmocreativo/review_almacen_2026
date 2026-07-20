<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\OrdenArticulo;
use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminOrdenCompraController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenCompra::withCount('articulos');

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where(function ($q) use ($b) {
                $q->where('ID_ORDEN', 'like', "%$b%")
                  ->orWhere('FOLIO_FACTURA', 'like', "%$b%");
            });
        }

        $ordenes = $query->orderBy('FECHA_REGISTRO', 'desc')->paginate(15)->withQueryString();

        return view('admin.ordenes-compra.index', compact('ordenes'));
    }

    public function create()
    {
        return view('admin.ordenes-compra.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_ORDEN'       => 'required|string|max:255|unique:al_ordenes_compra,ID_ORDEN',
            'FOLIO_FACTURA'  => 'nullable|string|max:255',
            'FECHA_REGISTRO' => 'required|date',
            'FACTURA_PDF'    => 'nullable|file|mimes:pdf|max:10240',
            'FACTURA_XML'    => 'nullable|file|mimes:xml,text|max:10240',
            'IMPORTE_FACTURA' => 'nullable|numeric|min:0',
        ]);

        $pdf = '';
        $xml = '';

        if ($request->hasFile('FACTURA_PDF')) {
            $pdf = $request->file('FACTURA_PDF')->store('facturas', 'public');
        }
        if ($request->hasFile('FACTURA_XML')) {
            $xml = $request->file('FACTURA_XML')->store('facturas', 'public');
        }

        $orden = OrdenCompra::create([
            'ID_ORDEN'       => strtoupper($request->ID_ORDEN),
            'FOLIO_FACTURA'  => $request->FOLIO_FACTURA ?? '',
            'FECHA_REGISTRO' => $request->FECHA_REGISTRO,
            'IMPORTE_FACTURA' => $request->IMPORTE_FACTURA ?? null,
            'FACTURA_PDF'    => $pdf,
            'FACTURA_XML'    => $xml,
        ]);

        if ($request->accion === 'articulos') {
            return redirect()->route('admin.ordenes.articulos', $orden->ID_ORDEN)
                ->with('success', 'Orden creada. Ahora agrega los artículos.');
        }

        return redirect()->route('admin.ordenes.index')
            ->with('success', 'Orden de compra creada correctamente.');
    }

    public function show(OrdenCompra $orden)
    {
        $articulos = $orden->articulos()->get();
        return view('admin.ordenes-compra.show', compact('orden', 'articulos'));
    }

    public function edit(OrdenCompra $orden)
    {
        return view('admin.ordenes-compra.edit', compact('orden'));
    }

    public function update(Request $request, OrdenCompra $orden)
    {
        $request->validate([
            'ID_ORDEN'       => 'required|string|max:255|unique:al_ordenes_compra,ID_ORDEN,' . $orden->ID . ',ID',
            'FOLIO_FACTURA'  => 'nullable|string|max:255',
            'FECHA_REGISTRO' => 'required|date',
            'IMPORTE_FACTURA' => 'nullable|numeric|min:0',
            'FACTURA_PDF'    => 'nullable|file|mimes:pdf|max:10240',
            'FACTURA_XML'    => 'nullable|file|mimes:xml,text|max:10240',
        ]);

        $data = [
            'ID_ORDEN'       => strtoupper($request->ID_ORDEN),
            'FOLIO_FACTURA'  => $request->FOLIO_FACTURA ?? '',
            'FECHA_REGISTRO' => $request->FECHA_REGISTRO,
            'IMPORTE_FACTURA' => $request->IMPORTE_FACTURA ?? null,
        ];

        if ($request->hasFile('FACTURA_PDF')) {
            if ($orden->FACTURA_PDF) Storage::disk('public')->delete($orden->FACTURA_PDF);
            $data['FACTURA_PDF'] = $request->file('FACTURA_PDF')->store('facturas', 'public');
        }
        if ($request->hasFile('FACTURA_XML')) {
            if ($orden->FACTURA_XML) Storage::disk('public')->delete($orden->FACTURA_XML);
            $data['FACTURA_XML'] = $request->file('FACTURA_XML')->store('facturas', 'public');
        }

        $orden->update($data);

        return redirect()->route('admin.ordenes.index')
            ->with('success', 'Orden actualizada correctamente.');
    }

    public function destroy(OrdenCompra $orden)
    {
        if ($orden->FACTURA_PDF) Storage::disk('public')->delete($orden->FACTURA_PDF);
        if ($orden->FACTURA_XML) Storage::disk('public')->delete($orden->FACTURA_XML);
        $orden->articulos()->delete();
        $orden->delete();

        return redirect()->route('admin.ordenes.index')
            ->with('success', 'Orden eliminada correctamente.');
    }

    // ── Vista de artículos de la orden ──
    public function articulos(string $idOrden)
    {
        $orden    = OrdenCompra::where('ID_ORDEN', $idOrden)->firstOrFail();
        $articulos = $orden->articulos()->get();
        return view('admin.ordenes-compra.articulos', compact('orden', 'articulos'));
    }

    // ── Buscar artículo para agregar a la orden ──
    public function buscarArticulo(Request $request)
    {
        $request->validate([
            'busqueda'      => 'required|string',
            'tipo_busqueda' => 'required|in:folio,serie',
            'id_orden'      => 'required|string',
        ]);

        $busqueda = strtoupper(trim($request->busqueda));
        $idOrden  = $request->id_orden;

        if ($request->tipo_busqueda === 'serie') {
            $articulo = Articulo::where('SERIE', $busqueda)->first();

            if (!$articulo) {
                return response()->json(['tipo' => 'serie_nueva', 'serie' => $busqueda]);
            }

            // Verificar si ya está en una orden
            $enOrden = OrdenArticulo::where('SERIE', $busqueda)->exists();

            return response()->json([
                'tipo'     => 'serie_encontrada',
                'articulo' => $articulo,
                'en_orden' => $enOrden,
            ]);
        }

        // Búsqueda por folio
        $articulos = Articulo::where('FOLIO', $busqueda)->get();

        if ($articulos->isEmpty()) {
            return response()->json(['tipo' => 'folio_nuevo', 'folio' => $busqueda]);
        }

        $tieneSeries = $articulos->first()->tieneSerie();

        if ($tieneSeries) {
            // Filtrar series que NO están ya en alguna orden
            $seriesEnOrdenes = OrdenArticulo::whereIn('SERIE', $articulos->pluck('SERIE'))
                ->pluck('SERIE')->toArray();

            $disponibles = $articulos->filter(fn($a) => !in_array($a->SERIE, $seriesEnOrdenes))->values();

            return response()->json([
                'tipo'        => 'folio_con_series',
                'folio'       => $busqueda,
                'disponibles' => $disponibles,
                'total'       => $articulos->count(),
                'nombre'      => $articulos->first()->NOMBRE,
                'formato'     => $articulos->first()->FORMATO,
                'costo'       => $articulos->first()->COSTO_UNITARIO,
            ]);
        }

        return response()->json([
            'tipo'     => 'folio_granel',
            'folio'    => $busqueda,
            'articulo' => $articulos->first(),
        ]);
    }

    // ── Agregar artículo(s) a la orden ──
    public function agregarArticulo(Request $request)
    {
        $request->validate([
            'id_orden'   => 'required|string',
            'tipo'       => 'required|in:series,granel,nuevo',
            'ids'        => 'required_if:tipo,series|array',
            'cantidad'   => 'required_if:tipo,granel|integer|min:1',
            'folio'      => 'required_if:tipo,nuevo|string',
            'nombre'     => 'required_if:tipo,nuevo|string',
        ]);

        $idOrden = strtoupper($request->id_orden);
        $orden   = OrdenCompra::where('ID_ORDEN', $idOrden)->firstOrFail();

        DB::transaction(function () use ($request, $idOrden) {

            if ($request->tipo === 'series') {
                $articulos = Articulo::whereIn('ID_ARTICULO', $request->ids)->get();
                foreach ($articulos as $articulo) {
                    OrdenArticulo::create([
                        'ID_ORDEN'      => $idOrden,
                        'ID_ARTICULO'   => $articulo->ID_ARTICULO,
                        'FOLIO'         => $articulo->FOLIO,
                        'SERIE'         => $articulo->SERIE,
                        'SERIE_NUMERICO'=> $articulo->SERIE_NUMERICO,
                        'FORMATO'       => $articulo->FORMATO,
                        'CANTIDAD'      => 1,
                        'COSTO_UNITARIO'=> $articulo->COSTO_UNITARIO,
                    ]);
                    $articulo->increment('CANTIDAD_ALMACEN');
                }

            } elseif ($request->tipo === 'granel') {
                $articulo = Articulo::findOrFail($request->id_articulo);
                OrdenArticulo::create([
                    'ID_ORDEN'      => $idOrden,
                    'ID_ARTICULO'   => $articulo->ID_ARTICULO,
                    'FOLIO'         => $articulo->FOLIO,
                    'SERIE'         => '',
                    'SERIE_NUMERICO'=> '',
                    'FORMATO'       => $articulo->FORMATO,
                    'CANTIDAD'      => $request->cantidad,
                    'COSTO_UNITARIO'=> $articulo->COSTO_UNITARIO,
                ]);
                $articulo->increment('CANTIDAD_ALMACEN', $request->cantidad);

            } elseif ($request->tipo === 'nuevo') {
                $cantidad = $request->filled('SERIE') ? 1 : ($request->cantidad ?? 0);
                $articulo = Articulo::create([
                    'FOLIO'                => strtoupper($request->folio),
                    'SERIE'                => strtoupper($request->SERIE ?? ''),
                    'SERIE_NUMERICO'       => $request->SERIE_NUMERICO ?? '',
                    'NOMBRE'               => $request->nombre,
                    'DESCRIPCION'          => $request->descripcion ?? '',
                    'FORMATO'              => strtoupper($request->formato ?? ''),
                    'COSTO_UNITARIO'       => $request->costo_unitario ?? 0,
                    'PRECIO_VENTA'         => 0,
                    'CANTIDAD_ALMACEN'     => $cantidad,
                    'CANTIDAD_SOLICITUDES' => 0,
                    'CANTIDAD_DESTRUCCION' => 0,
                    'CANTIDAD_PERDIDOS'    => 0,
                    'UBICACION_UNICA'      => 'almacen',
                    'TIPO'                 => $request->tipo_articulo ?? 'fisico',
                ]);
                OrdenArticulo::create([
                    'ID_ORDEN'      => $idOrden,
                    'ID_ARTICULO'   => $articulo->ID_ARTICULO,
                    'FOLIO'         => $articulo->FOLIO,
                    'SERIE'         => $articulo->SERIE,
                    'SERIE_NUMERICO'=> $articulo->SERIE_NUMERICO,
                    'FORMATO'       => $articulo->FORMATO,
                    'CANTIDAD'      => $cantidad,
                    'COSTO_UNITARIO'=> $articulo->COSTO_UNITARIO,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    // ── Eliminar artículo de la orden (y revertir cantidad) ──
    public function eliminarArticulo(Request $request, int $id)
    {
        $item = OrdenArticulo::findOrFail($id);
        $articulo = Articulo::find($item->ID_ARTICULO);

        if ($articulo) {
            $articulo->decrement('CANTIDAD_ALMACEN', $item->CANTIDAD);
        }

        $item->delete();

        return response()->json(['success' => true]);
    }
}