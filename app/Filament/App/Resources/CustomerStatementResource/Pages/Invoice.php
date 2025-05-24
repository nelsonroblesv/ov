<?php

namespace App\Filament\App\Resources\CustomerStatementResource\Pages;

use App\Filament\App\Resources\CustomerStatementResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payments;
use Filament\Resources\Pages\Page;

class Invoice extends Page
{
    protected static string $resource = CustomerStatementResource::class;

     protected static ?string $title = 'Estado de Cuenta';
    protected static ?string $navigationLabel = 'Detalles';
    protected static ?string $breadcrumb = "Detalles";

    protected static string $view = 'filament.app.resources.customer-statement-resource.pages.invoice';

     public $record;
    public $customer;
    public $order;
    public $payment;

    public function mount($record): void
    {
        $this->record = $record;
        $this->customer = Customer::find($record);

        $this->order = Order::where('customer_id', $record)->get();
        $this->payment = Payments::where('customer_id', $record)->where('is_verified', true)->get();
    }
}
