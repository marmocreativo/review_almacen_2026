<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;

class AdminBitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::with('usuario')->latest('created_at');

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('id_user')) {
            $query->where('id_user', $request->id_user);
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }
        if ($request->filled('q')) {
            $query->where('descripcion', 'like', '%' . $request->q . '%');
        }

        $registros = $query->paginate(30)->withQueryString();

        $modulos  = Bitacora::select('modulo')->distinct()->orderBy('modulo')->pluck('modulo');
        $acciones = Bitacora::select('accion')->distinct()->orderBy('accion')->pluck('accion');
        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('admin.bitacora.index', compact('registros', 'modulos', 'acciones', 'usuarios'));
    }
}