<?php

namespace App\Filament\Resources\RutasUsuariosResource\Pages;

use App\Filament\Resources\RutasUsuariosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRutasUsuarios extends ListRecords
{
    protected static string $resource = RutasUsuariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
