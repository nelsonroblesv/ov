<?php

namespace App\Filament\Resources\AdministrarTicketsResource\Pages;

use App\Filament\Resources\AdministrarTicketsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdministrarTickets extends EditRecord
{
    protected static string $resource = AdministrarTicketsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
