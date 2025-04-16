<?php

namespace App\Filament\Resources\UbicacionUsuarioResource\Pages;

use App\Filament\Resources\UbicacionUsuarioResource;
use App\Filament\Resources\UbicacionUsuarioResource\Widgets\UbicacionUsuarioMap;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUbicacionUsuarios extends ListRecords
{
    protected static string $resource = UbicacionUsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UbicacionUsuarioMap::class,
        ];
    }
}
