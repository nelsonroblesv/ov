<?php

namespace App\Filament\Resources\CustomerStatementResource\Pages;

use App\Filament\Resources\CustomerStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerStatements extends ListRecords
{
    protected static string $resource = CustomerStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
