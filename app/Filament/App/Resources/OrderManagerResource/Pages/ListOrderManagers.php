<?php

namespace App\Filament\App\Resources\OrderManagerResource\Pages;

use App\Filament\App\Resources\OrderManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderManagers extends ListRecords
{
    protected static string $resource = OrderManagerResource::class;
     protected static ?string $title = 'Gestionar Pedidos y Pagos';

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
