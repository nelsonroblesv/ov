<?php

namespace App\Filament\Resources\ZoneLocationResource\Pages;

use App\Filament\Resources\ZoneLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListZoneLocations extends ListRecords
{
    protected static string $resource = ZoneLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
