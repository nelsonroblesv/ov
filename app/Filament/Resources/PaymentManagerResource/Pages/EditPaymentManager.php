<?php

namespace App\Filament\Resources\PaymentManagerResource\Pages;

use App\Filament\Resources\PaymentManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentManager extends EditRecord
{
    protected static string $resource = PaymentManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
