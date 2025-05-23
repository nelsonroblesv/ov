<?php

namespace App\Filament\Resources\CustomerStatementResource\Pages;

use App\Filament\Resources\CustomerStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerStatement extends CreateRecord
{
    protected static string $resource = CustomerStatementResource::class;
}
