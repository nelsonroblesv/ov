<?php

use App\Http\Controllers\OrdenPDFController;
use App\Http\Controllers\UbicacionUsuarioController;
use App\Models\UbicacionUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
   return view('home');
});
*/

Route::get('ReporteIndividual/{order}', OrdenPDFController::class)->name('ReporteIndividual'); 
Route::post('/guardar-ubicacion', function (Request $request) {
   try {
       UbicacionUsuario::create([
           'user_id' => auth()->id(),
           'latitud' => $request->input('latitud'),
           'longitud' => $request->input('longitud'),
       ]);

       return response()->json(['status' => 'ok']);
   } catch (\Throwable $e) {
       Log::error('Error al guardar ubicación: ' . $e->getMessage());
       return response()->json(['status' => 'error', 'message' => 'Error al guardar ubicación'], 500);
   }
});
