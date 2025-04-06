<?php

namespace App\Filament\Resources\AdministrarRutasResource\Pages;

use App\Filament\Resources\AdministrarRutasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdministrarRutas extends ListRecords
{
    protected static string $resource = AdministrarRutasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
