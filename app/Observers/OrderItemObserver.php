<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        $existingItem = OrderItem::where('order_id', $orderItem->order_id)
        ->where('product_id', $orderItem->product_id)
        ->where('id', '!=', $orderItem->id) // Excluye el registro actual
        ->first();

    if ($existingItem) {
        // Actualizar cantidad y total en el registro existente
        $existingItem->quantity += $orderItem->quantity;
        $existingItem->total_price += $orderItem->total_price;
        $existingItem->save();

        // Eliminar el nuevo registro para evitar duplicados
        $orderItem->delete();
    }


        $this->updateOrderTotal($orderItem->order_id);
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        $this->updateOrderTotal($orderItem->order_id);
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        $this->updateOrderTotal($orderItem->order_id);
    }

    /**
     * Handle the OrderItem "restored" event.
     */
    public function restored(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     */
    public function forceDeleted(OrderItem $orderItem): void
    {
        //
    }

    private function updateOrderTotal($orderId)
    {
        // Sumar todos los valores de total_price para el order_id proporcionado
        $total = OrderItem::where('order_id', $orderId)->sum('total_price');

        // Actualizar el campo grand_total en la tabla orders
        Order::where('id', $orderId)->update(['grand_total' => $total]);
    }
}
