<?php

namespace App\Http\Controllers;

use App\Models\UbicacionUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UbicacionUsuarioController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Usuario autenticado:', ['user_id' => auth()->id()]);

        $request->validate([
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        UbicacionUsuario::create([
            'user_id' => auth()->id(),
            'latitud' => $request->lat,
            'longitud' => $request->lng,
        ]);

        return response()->json(['status' => 'success']);
    }
}
