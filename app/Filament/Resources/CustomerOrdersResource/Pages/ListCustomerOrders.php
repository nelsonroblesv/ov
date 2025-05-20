<?php

namespace App\Filament\Resources\CustomerOrdersResource\Pages;

use App\Filament\Resources\CustomerOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerOrders extends ListRecords
{
    protected static string $resource = CustomerOrdersResource::class;
    protected static ?string $title = 'Gestionar Pedidos';


    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
