<?php

namespace App\Filament\App\Resources\CustomerUserResource\Pages;

use App\Filament\App\Resources\CustomerUserResource;
use App\Filament\Resources\CustomerUserResource\Widgets\CustomersMap;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerUsers extends ListRecords
{
    protected static string $resource = CustomerUserResource::class;
    protected static ?string $title = 'Mis Clientes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Cliente')
                ->icon('heroicon-o-user-plus')
                ->color('success'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
           // CustomersMap::class,
        ];
    }
}