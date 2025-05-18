<?php

namespace App\Filament\Resources\PaqueteGuiasResource\Pages;

use App\Filament\Resources\PaqueteGuiasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaqueteGuias extends EditRecord
{
    protected static string $resource = PaqueteGuiasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
