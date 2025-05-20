<?php

namespace App\Filament\Resources\CustomerOrdersResource\Pages;

use App\Filament\Resources\CustomerOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerOrders extends EditRecord
{
    protected static string $resource = CustomerOrdersResource::class;
     protected static ?string $title = 'Editar Pedidos del Cliente';

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }
}
