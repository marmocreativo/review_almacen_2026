<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Sede;
use App\Models\Solicitud;
use App\Models\SolicitudExamen;
use App\Models\TipoExamen;
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

        session([
            'portal_contacto_id' => $contacto->id,
            'portal_pin'         => strtoupper($request->pin),
        ]);

        return redirect()->route('portal.solicitudes.historial');
    }

    public function salir(Request $request)
    {
        $request->session()->forget(['portal_contacto_id', 'portal_pin']);
        return redirect()->route('portal.login');
    }

    // ── HISTORIAL DE SOLICITUDES ──
    public function historial()
    {
        $contacto = $this->contactoActual();

        $solicitudes = Solicitud::where('ID_CONTACTO', $contacto->id)
            ->with('sede')
            ->withCount('examenes')
            ->orderBy('FECHA_SOLICITUD', 'desc')
            ->paginate(10);

        return view('portal.historial', [
            'contacto'    => $contacto,
            'solicitudes' => $solicitudes,
        ]);
    }

    // ── SHOW: solo datos generales + estado ──
    public function show(Solicitud $solicitud)
    {
        $contacto = $this->contactoActual();

        if ($solicitud->ID_CONTACTO != $contacto->id) {
            abort(403, 'Acceso no autorizado.');
        }

        $solicitud->load('examenes', 'empresa', 'sede', 'contacto');

        return view('portal.show', ['solicitud' => $solicitud]);
    }

    // ── CREATE: formulario de un solo paso con repeater de exámenes ──
    public function create()
    {
        $contacto = $this->contactoActual();
        $contacto->load('sedes', 'empresa');

        $tipoExamenes = TipoExamen::where('estado', 'activo')->orderBy('nombre')->get();

        return view('portal.create', [
            'contacto'     => $contacto,
            'tipoExamenes' => $tipoExamenes,
        ]);
    }

    public function store(Request $request)
    {
        $contacto = $this->contactoActual();

        $request->validate([
            'ID_SEDE'                    => 'required|exists:sedes,id',
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

        if (!$contacto->sedes()->where('sedes.id', $request->ID_SEDE)->exists()) {
            abort(403, 'Esta sede no está asignada a tu contacto.');
        }

        $sede = Sede::findOrFail($request->ID_SEDE);

        $diasMinimos = $request->ENVIO_ZONA === 'cdmx_area_metropolitana' ? 10 : 15;
        $fechaMinima = \Carbon\Carbon::now()->addDays($diasMinimos)->format('Y-m-d');

        if ($request->FECHA_PRIMERA_APLICACION < $fechaMinima) {
            return back()->withInput()->withErrors([
                'FECHA_PRIMERA_APLICACION' => "La fecha mínima de primer aplicación para esta zona es {$fechaMinima} ({$diasMinimos} días de anticipación).",
            ]);
        }

        $solicitud = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $contacto, $sede) {
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
                'ID_EMPRESA'               => $sede->id_empresa,
                'ID_SEDE'                  => $sede->id,
                'ID_CONTACTO'              => $contacto->id,
                'RESPONSABLE_TITULO'       => $request->RESPONSABLE_TITULO ?? '',
                'RESPONSABLE_NOMBRE'       => $request->RESPONSABLE_NOMBRE,
                'RESPONSABLE_CORREO'       => $request->RESPONSABLE_CORREO ?? '',
                'RESPONSABLE_TELEFONO'     => $request->RESPONSABLE_TELEFONO ?? '',
                'RESPONSABLE_CELULAR'      => '',
                'DIRECCION_ENVIO'          => $request->DIRECCION_ENVIO ?? '',
                'SESIONES_SIMULTANEAS'     => $request->SESIONES_SIMULTANEAS,
                'CANTIDAD_SIMULTANEAS'     => $request->CANTIDAD_SIMULTANEAS ?? null,
                'HORARIO_DE_ATENCION'      => '',
                'FECHA_PRIMERA_APLICACION' => $request->FECHA_PRIMERA_APLICACION,
                'CANTIDAD_USB'             => $request->CANTIDAD_USB ?? 0,
                'CANTIDAD_CD'              => $request->CANTIDAD_CD ?? 0,
                'OBSERVACIONES'            => $request->OBSERVACIONES ?? '',
                'ENVIO_ZONA'               => $request->ENVIO_ZONA,
                'ENVIO_EXAMEN'             => $nombresExamenes,
                'ENVIO_NUMERO_HOJAS'       => $totalCandidatos,
                'CANTIDAD_EXAMENES'        => 0,
                'CANTIDAD_EXAMENES_APLICADOS' => 0,
                'ESTADO_SOLICITUD'         => 'pendiente',
                'APROBACION'               => 'pendiente',
                'ESTADO_FACTURA'           => 'pendiente',
                'FECHA_SOLICITUD'          => now(),
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

        return redirect()->route('portal.solicitudes.show', $solicitud->ID_SOLICITUD)
            ->with('success', 'Tu solicitud fue registrada correctamente.');
    }

    private function contactoActual(): Contacto
    {
        $contactoId = session('portal_contacto_id');
        $pin        = session('portal_pin');

        if (!$contactoId || !$pin) {
            abort(redirect()->route('portal.login')->withErrors(['pin' => 'Tu sesión expiró, ingresa tu PIN nuevamente.']));
        }

        $contacto = Contacto::find($contactoId);

        if (!$contacto || $contacto->pin !== $pin) {
            session()->forget(['portal_contacto_id', 'portal_pin']);
            abort(redirect()->route('portal.login')->withErrors(['pin' => 'Tu sesión ya no es válida, ingresa tu PIN nuevamente.']));
        }

        return $contacto;
    }
}