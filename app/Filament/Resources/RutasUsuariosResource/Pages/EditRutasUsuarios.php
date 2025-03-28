<?php

namespace App\Filament\Resources\RutasUsuariosResource\Pages;

use App\Filament\Resources\RutasUsuariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRutasUsuarios extends EditRecord
{
    protected static string $resource = RutasUsuariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
