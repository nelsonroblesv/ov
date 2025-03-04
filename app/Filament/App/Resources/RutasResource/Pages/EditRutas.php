<?php

namespace App\Filament\App\Resources\RutasResource\Pages;

use App\Filament\App\Resources\RutasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRutas extends EditRecord
{
    protected static string $resource = RutasResource::class;

    protected function getHeaderActions(): array
    {
        return [
         //   Actions\DeleteAction::make(),
        ];
    }
}
