<?php

namespace App\Filament\Resources\CustomerStatementResource\Pages;

use App\Filament\Resources\CustomerStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerStatement extends EditRecord
{
    protected static string $resource = CustomerStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
