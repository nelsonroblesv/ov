<?php

namespace App\Filament\Resources\CustomerStatementResource\Pages;

use App\Filament\Resources\CustomerStatementResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payments;
use DragonCode\Contracts\Cashier\Config\Payment;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\Modal\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;

class Invoice extends Page
{
    protected static string $resource = CustomerStatementResource::class;

    protected static ?string $title = 'Estado de Cuenta';
    protected static ?string $navigationLabel = 'Detalles';
    protected static ?string $breadcrumb = "Detalles";

    protected static string $view = 'filament.resources.customer-statement-resource.pages.invoice';

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

    public function getHeaderActions(): array
    {
        return [
            ActionsAction::make('print')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(route('PRINT.CUSTOMER_INVOICE', ['id'=>$this->record]))
                ->openUrlInNewTab()
                ->requiresConfirmation()
                ->modalHeading('Imprimir Estado de Cuenta')
                ->modalDescription('¿Estas seguro de que deseas imprimir el estado de cuenta?'),
        ];
    }
    
}
