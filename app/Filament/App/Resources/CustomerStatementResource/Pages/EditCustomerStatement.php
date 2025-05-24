<?php

namespace App\Filament\App\Resources\CustomerStatementResource\Pages;

use App\Filament\App\Resources\CustomerStatementResource;
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
