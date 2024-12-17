<?php

namespace App\Filament\Resources\ZoneCustomerResource\Pages;

use App\Filament\Resources\ZoneCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditZoneCustomer extends EditRecord
{
    protected static string $resource = ZoneCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
