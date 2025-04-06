<?php

namespace App\Filament\Resources\CambiarRutasResource\Pages;

use App\Filament\Resources\CambiarRutasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCambiarRutas extends EditRecord
{
    protected static string $resource = CambiarRutasResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\DeleteAction::make(),
        ];
    }
}
