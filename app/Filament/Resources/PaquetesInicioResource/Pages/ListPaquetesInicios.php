<?php

namespace App\Filament\Resources\PaquetesInicioResource\Pages;

use App\Filament\Resources\PaquetesInicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaquetesInicios extends ListRecords
{
    protected static string $resource = PaquetesInicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
