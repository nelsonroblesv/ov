<?php

namespace App\Filament\Resources\PaymentManagerResource\Pages;

use App\Filament\Resources\PaymentManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentManagers extends ListRecords
{
    protected static string $resource = PaymentManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
