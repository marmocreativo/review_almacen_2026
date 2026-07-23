<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PinController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits_between:4,8|confirmed',
        ]);

        $request->user()->update([
            'pin'        => Hash::make($request->pin),
            'pin_set_at' => now(),
        ]);

        return back()->with('success', 'PIN configurado correctamente.');
    }

    public function verificar(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $user = $request->user();

        if (!$user->tienePin()) {
            return response()->json(['success' => false, 'message' => 'No tienes un PIN configurado.'], 422);
        }

        if (!$user->verificarPin($request->pin)) {
            return response()->json(['success' => false, 'message' => 'PIN incorrecto.'], 422);
        }

        return response()->json(['success' => true]);
    }
}