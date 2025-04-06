<?php

namespace App\Filament\Resources\CambiarRutasResource\Pages;

use App\Filament\Resources\CambiarRutasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCambiarRutas extends ListRecords
{
    protected static string $resource = CambiarRutasResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
