<?php

namespace App\Filament\Resources\UbicacionUsuarioResource\Pages;

use App\Filament\Resources\UbicacionUsuarioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUbicacionUsuario extends EditRecord
{
    protected static string $resource = UbicacionUsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\DeleteAction::make(),
        ];
    }
}
