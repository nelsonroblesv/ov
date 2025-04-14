<?php

use App\Http\Controllers\OrdenPDFController;
use App\Http\Controllers\UbicacionUsuarioController;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
   return view('home');
});
*/

Route::get('ReporteIndividual/{order}', OrdenPDFController::class)->name('ReporteIndividual'); 
Route::middleware(['auth'])->post('/guardar-ubicacion', [UbicacionUsuarioController::class, 'store']);

