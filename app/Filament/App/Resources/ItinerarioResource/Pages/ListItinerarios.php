<?php

namespace App\Filament\App\Resources\ItinerarioResource\Pages;

use App\Filament\App\Resources\ItinerarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItinerarios extends ListRecords
{
    protected static string $resource = ItinerarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }
}
