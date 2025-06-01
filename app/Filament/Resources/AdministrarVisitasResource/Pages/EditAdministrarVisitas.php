<?php

namespace App\Filament\Resources\AdministrarVisitasResource\Pages;

use App\Filament\Resources\AdministrarVisitasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdministrarVisitas extends EditRecord
{
    protected static string $resource = AdministrarVisitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\DeleteAction::make(),
        ];
    }
}
