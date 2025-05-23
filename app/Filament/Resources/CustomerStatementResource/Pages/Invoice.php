<?php

namespace App\Filament\Resources\CustomerStatementResource\Pages;

use App\Filament\Resources\CustomerStatementResource;
use Filament\Resources\Pages\Page;

class Invoice extends Page
{
    protected static string $resource = CustomerStatementResource::class;

    protected static ?string $title = 'Estado de Cuenta';
    protected static ?string $navigationLabel = 'Detalles';
    protected static ?string $breadcrumb = "Detalles";

    protected static string $view = 'filament.resources.customer-statement-resource.pages.invoice';

    public $record;
    public $orders;
    public $payments;

    public function mount($record): void
    {
        $this->record = $record;
        //$this->orders = $record->orders()->with('customer')->get();
        //$this->payments = $record->payments()->with('customer')->get();
    }



}
