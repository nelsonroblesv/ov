<?php

namespace App\Filament\App\Resources\GestionRutasResource\Pages;

use App\Filament\App\Resources\GestionRutasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGestionRutas extends ListRecords
{
    protected static string $resource = GestionRutasResource::class;
    protected static ?string $title = 'Registros no asignados a Ruta';

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
