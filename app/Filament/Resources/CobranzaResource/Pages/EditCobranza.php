<?php

namespace App\Filament\Resources\CobranzaResource\Pages;

use App\Filament\Resources\CobranzaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCobranza extends EditRecord
{
    protected static string $resource = CobranzaResource::class;
    protected static ?string $title = 'Editar Saldo Deudor';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
