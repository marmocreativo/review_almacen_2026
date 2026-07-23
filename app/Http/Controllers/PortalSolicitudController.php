<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Sede;
use App\Models\Solicitud;
use App\Models\SolicitudExamen;
use App\Models\TipoExamen;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PortalSolicitudController extends Controller
{
    public function login()
    {
        return view('portal.login');
    }

    public function verificarPin(Request $request)
    {
        $request->validate(['pin' => 'required|string|size:8']);

        $contacto = Contacto::where('pin', strtoupper($request->pin))->first();

        if (!$contacto) {
            return back()->withErrors(['pin' => 'PIN inválido.'])->withInput();
        }

        $contacto->load('sedes', 'empresa');

        return view('portal.create', ['contacto' => $contacto, 'pin' => strtoupper($request->pin)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pin'                  => 'required|string|size:8',
            'contacto_id'          => 'required|integer|exists:contactos,id',
            'ID_SEDE'              => 'required|exists:sedes,id',
            'RESPONSABLE_NOMBRE'   => 'required|string|max:255',
            'RESPONSABLE_CORREO'   => 'nullable|email|max:255',
            'RESPONSABLE_TELEFONO' => 'nullable|string|max:20',
            'RESPONSABLE_CELULAR'  => 'nullable|string|max:20',
            'DIRECCION_ENVIO'      => 'nullable|string',
            'SESIONES_SIMULTANEAS' => 'required|in:si,no',
            'HORARIO_DE_ATENCION'  => 'nullable|string',
            'OBSERVACIONES'        => 'nullable|string',
            'ENVIO_ZONA'           => 'nullable|in:cdmx_area_metropolitana,foraneo',
        ]);

        $contacto = Contacto::find($request->contacto_id);

        if (!$contacto || $contacto->pin !== strtoupper($request->pin)) {
            abort(403, 'Acceso no autorizado.');
        }

        if (!$contacto->sedes()->where('sedes.id', $request->ID_SEDE)->exists()) {
            abort(403, 'Esta sede no está asignada a tu contacto.');
        }

        $sede = Sede::findOrFail($request->ID_SEDE);

        $solicitud = Solicitud::create([
            'ID_EMPRESA'              => $sede->id_empresa,
            'ID_SEDE'                 => $sede->id,
            'ID_CONTACTO'             => $contacto->id,
            'RESPONSABLE_NOMBRE'      => $request->RESPONSABLE_NOMBRE,
            'RESPONSABLE_CORREO'      => $request->RESPONSABLE_CORREO ?? '',
            'RESPONSABLE_TELEFONO'    => $request->RESPONSABLE_TELEFONO ?? '',
            'RESPONSABLE_CELULAR'     => $request->RESPONSABLE_CELULAR ?? '',
            'DIRECCION_ENVIO'         => $request->DIRECCION_ENVIO ?? '',
            'SESIONES_SIMULTANEAS'    => $request->SESIONES_SIMULTANEAS,
            'HORARIO_DE_ATENCION'     => $request->HORARIO_DE_ATENCION ?? '',
            'OBSERVACIONES'           => $request->OBSERVACIONES ?? '',
            'ENVIO_ZONA'              => $request->ENVIO_ZONA,
            'CANTIDAD_EXAMENES'       => 0,
            'CANTIDAD_EXAMENES_APLICADOS' => 0,
            'ESTADO_SOLICITUD'        => 'pendiente',
            'ESTADO_FACTURA'          => 'pendiente',
            'FECHA_SOLICITUD'         => now(),
        ]);

        return redirect()->route('portal.solicitudes.examenes', [
            'solicitud' => $solicitud->ID_SOLICITUD,
            'pin'       => strtoupper($request->pin),
            'contacto'  => $contacto->id,
        ]);
    }

    public function examenes(Request $request, Solicitud $solicitud)
    {
        $this->autorizar($request, $solicitud);

        $solicitud->load('examenes', 'empresa', 'sede');
        $tipoExamenes = TipoExamen::where('estado', 'activo')->orderBy('nombre')->get();

        return view('portal.examenes', [
            'solicitud'    => $solicitud,
            'tipoExamenes' => $tipoExamenes,
            'pin'          => strtoupper($request->pin),
            'contactoId'   => $request->contacto,
        ]);
    }

    public function agregarExamen(Request $request, Solicitud $solicitud)
    {
        $this->autorizar($request, $solicitud);

        $request->validate([
            'tipo_examen_id' => 'required|exists:tipo_examenes,id',
            'cantidad'       => 'required|integer|min:1',
            'fecha'          => 'required|date',
        ]);

        $tipo = TipoExamen::findOrFail($request->tipo_examen_id);
        $fechaMinima = Carbon::now()->addDays($tipo->dias_anticipacion)->format('Y-m-d');

        if ($request->fecha < $fechaMinima) {
            return back()->withErrors(['fecha' => "La fecha mínima para este examen es {$fechaMinima} ({$tipo->dias_anticipacion} días de anticipación)."])->withInput();
        }

        if ($request->cantidad < $tipo->candidatos_minimos) {
            return back()->withErrors(['cantidad' => "La cantidad mínima de candidatos para este examen es {$tipo->candidatos_minimos}."])->withInput();
        }

        SolicitudExamen::create([
            'ID_SOLICITUD' => $solicitud->ID_SOLICITUD,
            'EXAMEN'       => $tipo->nombre,
            'CANTIDAD'     => $request->cantidad,
            'ESTADO'       => 'pendiente',
            'FECHA'        => $request->fecha,
        ]);

        $solicitud->increment('CANTIDAD_EXAMENES', $request->cantidad);

        return redirect()->route('portal.solicitudes.examenes', [
            'solicitud' => $solicitud->ID_SOLICITUD,
            'pin'       => $request->pin,
            'contacto'  => $request->contacto,
        ])->with('success', 'Examen agregado correctamente.');
    }

    public function eliminarExamen(Request $request, Solicitud $solicitud, SolicitudExamen $examen)
    {
        $this->autorizar($request, $solicitud);

        $solicitud->decrement('CANTIDAD_EXAMENES', $examen->CANTIDAD);
        $examen->delete();

        return redirect()->route('portal.solicitudes.examenes', [
            'solicitud' => $solicitud->ID_SOLICITUD,
            'pin'       => $request->pin,
            'contacto'  => $request->contacto,
        ])->with('success', 'Examen eliminado correctamente.');
    }

    private function autorizar(Request $request, Solicitud $solicitud): void
    {
        $request->validate([
            'pin'      => 'required|string|size:8',
            'contacto' => 'required|integer',
        ]);

        $contacto = Contacto::find($request->contacto);

        if (!$contacto || $contacto->pin !== strtoupper($request->pin) || $solicitud->ID_CONTACTO != $contacto->id) {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function resumen(Request $request, Solicitud $solicitud)
    {
        $this->autorizar($request, $solicitud);

        $solicitud->load('examenes', 'empresa', 'sede', 'contacto');

        return view('portal.resumen', ['solicitud' => $solicitud]);
    }
}