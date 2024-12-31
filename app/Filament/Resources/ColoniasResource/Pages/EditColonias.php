<?php

namespace App\Filament\Resources\ColoniasResource\Pages;

use App\Filament\Resources\ColoniasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditColonias extends EditRecord
{
    protected static string $resource = ColoniasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
