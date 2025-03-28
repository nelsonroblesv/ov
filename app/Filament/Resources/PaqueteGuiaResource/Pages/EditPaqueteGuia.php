<?php

namespace App\Filament\Resources\PaqueteGuiaResource\Pages;

use App\Filament\Resources\PaqueteGuiaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaqueteGuia extends EditRecord
{
    protected static string $resource = PaqueteGuiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
