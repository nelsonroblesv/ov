<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Pedido;
use App\Models\PedidoRecord;
use App\Models\Product;
use Barryvdh\DomPDF\PDF;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class NotaVentaController extends Controller
{
    public function printNotaVenta($id)
    {
        $pedido = Pedido::with('items.product')->find($id);
        $customer = Customer::find($pedido->customer_id);

        $productos = $pedido->items;

        $total = array_sum(array_column($productos->toArray(), 'total_price'));

        if ($pedido) {
            PedidoRecord::create([
                'pedido_id' => $id,
                'user_id' => Auth::id(),
            ]);

            $pdf = \PDF::loadView('pdf.nota_venta', compact('customer', 'pedido', 'productos', 'total'));
            return $pdf->stream();
        } else {
            Notification::make()
                ->title('Error')
                ->body('No se encontró el cliente.')
                ->danger()
                ->send();
            return redirect()->back();
        }
    }
}
