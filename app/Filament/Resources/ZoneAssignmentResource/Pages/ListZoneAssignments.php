<?php

namespace App\Filament\Resources\ZoneAssignmentResource\Pages;

use App\Filament\Resources\ZoneAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListZoneAssignments extends ListRecords
{
    protected static string $resource = ZoneAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
