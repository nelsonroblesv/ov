<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\PedidosItems;

class PedidosItemsObserver
{
    /**
     * Handle the PedidosItems "created" event.
     */
    public function created(PedidosItems $pedidoItem): void
    {
        $existingItem = PedidosItems::where('pedido_id', $pedidoItem->pedido_id)
        ->where('product_id', $pedidoItem->product_id)
        ->where('id', '!=', $pedidoItem->id)
        ->first();

        if ($existingItem) {
            $existingItem->quantity += $pedidoItem->quantity;
            $existingItem->total_price += $pedidoItem->total_price;
            $existingItem->save();

            $pedidoItem->delete();
        }

        $this->updateOrderTotal($pedidoItem->pedido_id);
    }

    /**
     * Handle the PedidosItems "updated" event.
     */
    public function updated(PedidosItems $pedidoItem): void
    {
        $this->updateOrderTotal($pedidoItem->pedido_id);
    }

    /**
     * Handle the PedidosItems "deleted" event.
     */
    public function deleted(PedidosItems $pedidoItem): void
    {
        $this->updateOrderTotal($pedidoItem->pedido_id);
    }

    /**
     * Handle the PedidosItems "restored" event.
     */
    public function restored(PedidosItems $pedidoItem): void
    {
        //
    }

    /**
     * Handle the PedidosItems "force deleted" event.
     */
    public function forceDeleted(PedidosItems $pedidoItem): void
    {
        //
    }

    private function updateOrderTotal($pedidoId)
    {
        // Sumar todos los valores de total_price para el order_id proporcionado
        $total = PedidosItems::where('pedido_id', $pedidoId)->sum('total_price');

        // Actualizar el campo grand_total en la tabla orders
       // Pedido::where('id', $pedidoId)->update(['grand_total' => $total]);
    }
}
