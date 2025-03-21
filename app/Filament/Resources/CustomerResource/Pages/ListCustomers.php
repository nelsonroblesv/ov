<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerResource\Widgets\CustomersMap;
use App\Filament\Resources\CustomerResource\Widgets\CustomersMapWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;
    protected static ?string $title = 'Clientes';

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
            CustomersMap::class,
        ];
    }
}
