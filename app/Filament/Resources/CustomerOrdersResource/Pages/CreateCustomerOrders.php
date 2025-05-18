<?php

namespace App\Filament\Resources\CustomerOrdersResource\Pages;

use App\Filament\Resources\CustomerOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerOrders extends CreateRecord
{
    protected static string $resource = CustomerOrdersResource::class;
}
