<?php

namespace App\Filament\App\Resources\CustomerStatementResource\Pages;

use App\Filament\App\Resources\CustomerStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerStatements extends ListRecords
{
    protected static string $resource = CustomerStatementResource::class;
    protected static ?string $title = 'Administrar Estados de Cuenta';

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
