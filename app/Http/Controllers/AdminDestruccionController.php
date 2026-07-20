<?php

namespace App\Http\Controllers;

use App\Models\SolicitudArticulo;
use Illuminate\Http\Request;

class AdminDestruccionController extends Controller
{
    public function index()
    {
        $cajas = SolicitudArticulo::where('CANTIDAD_A_DESTRUCCION', '>', 0)
            ->selectRaw('UBICACION_DESTRUCCION, COUNT(*) as total_articulos, SUM(CANTIDAD_A_DESTRUCCION) as total_cantidad')
            ->groupBy('UBICACION_DESTRUCCION')
            ->orderBy('UBICACION_DESTRUCCION')
            ->get();

        return view('admin.destruccion.index', compact('cajas'));
    }

    public function contenidoCaja(Request $request)
    {
        $caja = $request->caja;
        $query = SolicitudArticulo::where('UBICACION_DESTRUCCION', $caja)
            ->where('CANTIDAD_A_DESTRUCCION', '>', 0);

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where('NOMBRE', 'like', "%$b%");
        } else {
            $desde = $request->fecha_desde ?? now()->subMonth()->format('Y-m-d');
            $hasta = $request->fecha_hasta ?? now()->format('Y-m-d');
            $query->whereBetween('FECHA_RETORNO', [$desde, $hasta]);
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
}