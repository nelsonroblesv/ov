<?php

namespace App\Filament\Resources\PaquetesInicioResource\Pages;

use App\Filament\Resources\PaquetesInicioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaquetesInicio extends EditRecord
{
    protected static string $resource = PaquetesInicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
