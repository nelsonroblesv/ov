<?php

namespace App\Filament\App\Resources\ProspectosResource\Pages;

use App\Filament\App\Resources\ProspectosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProspectos extends EditRecord
{
    protected static string $resource = ProspectosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
