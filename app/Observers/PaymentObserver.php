<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Order;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        //
        $order = Order::where('id', $payment->order_id)->first();
        
        
        $order->grand_total = $order->grand_total -  $payment->amount;

        if($order->grand_total == 0){
            $order->status = 'completed';
        }

       $order->save();

    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
