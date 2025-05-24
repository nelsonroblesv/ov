<?php

namespace App\Filament\App\Resources\CustomerStatementResource\Pages;

use App\Filament\App\Resources\CustomerStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerStatement extends CreateRecord
{
    protected static string $resource = CustomerStatementResource::class;
}
