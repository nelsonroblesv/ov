<?php

namespace App\Filament\Resources\UbicacionUsuarioResource\Pages;

use App\Filament\Resources\UbicacionUsuarioResource;
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
}
