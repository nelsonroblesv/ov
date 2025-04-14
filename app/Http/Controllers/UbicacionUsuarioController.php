<?php

namespace App\Http\Controllers;

use App\Models\UbicacionUsuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
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
            'created_at' => Carbon::now()->setTimezone('America/Merida'),

        ]);

        return response()->json(['status' => 'success']);
    }
}
