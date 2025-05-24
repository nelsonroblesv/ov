<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payments;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function printCustomerInvoice($id)
    {

        $customer = Customer::find($id);
        $order = Order::where('customer_id', $id)->get();
        $payment = Payments::where('customer_id', $id)->where('is_verified', true)->get();

        if ($customer) {
            $pdf = \PDF::loadView('pdf.customer_invoice', compact('customer', 'order', 'payment'));
            return $pdf->stream('EC - ' . $customer->name . '.pdf');
        }else{
            Notification::make()
                ->title('Error')
                ->body('No se encontró el cliente.')
                ->danger()
                ->send();
            return redirect()->back();
        }
    }
}
