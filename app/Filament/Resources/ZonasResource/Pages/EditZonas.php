<?php

namespace App\Filament\Resources\ZonasResource\Pages;

use App\Filament\Resources\ZonasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditZonas extends EditRecord
{
    protected static string $resource = ZonasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
