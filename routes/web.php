<?php

use App\Filament\App\Resources\RutasResource;
use App\Filament\Resources\PedidosResource\Pages\POS;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotaVentaController;
use App\Http\Controllers\OrdenPDFController;
use App\Http\Controllers\UbicacionUsuarioController;
use App\Livewire\Cart;
use App\Models\UbicacionUsuario;
use App\Services\FacturamaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
   return view('home');
});
*/

Route::get('ReporteIndividual/{order}', OrdenPDFController::class)->name('ReporteIndividual'); 
Route::get('/print-nota/{id}', [NotaVentaController::class, 'printNotaVenta'])->name('PRINT.NOTA_VENTA');

/*
Route::post('/guardar-ubicacion', function (Request $request) {
   try {
       UbicacionUsuario::create([
           'user_id' => auth()->id(),
           'latitud' => $request->input('latitud'),
           'longitud' => $request->input('longitud'),
           'created_at' => Carbon::now()->setTimezone('America/Merida'),
       ]);

       return response()->json(['status' => 'ok']);
   } catch (\Throwable $e) {
       Log::error('Error al guardar ubicación: ' . $e->getMessage());
       return response()->json(['status' => 'error', 'message' => 'Error al guardar ubicación'], 500);
   }
});
*/