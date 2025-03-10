<?php

namespace App\Filament\Resources\FormulariosResource\Pages;

use App\Filament\Resources\FormulariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormularios extends EditRecord
{
    protected static string $resource = FormulariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
