<?php

namespace App\Filament\App\Resources\GestionRutasResource\Pages;

use App\Filament\App\Resources\GestionRutasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGestionRutas extends EditRecord
{
    protected static string $resource = GestionRutasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
