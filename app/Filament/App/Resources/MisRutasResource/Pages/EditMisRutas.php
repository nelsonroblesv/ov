<?php

namespace App\Filament\App\Resources\MisRutasResource\Pages;

use App\Filament\App\Resources\MisRutasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMisRutas extends EditRecord
{
    protected static string $resource = MisRutasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
