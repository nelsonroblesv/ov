<?php

namespace App\Filament\Resources\ZonasResource\Pages;

use App\Filament\Resources\ZonasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListZonas extends ListRecords
{
    protected static string $resource = ZonasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
