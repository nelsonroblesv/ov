<?php

use App\Http\Controllers\OrdenPDFController;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
   return view('home');
});
*/

Route::get('ReporteIndividual/{order}', OrdenPDFController::class)->name('ReporteIndividual'); 
