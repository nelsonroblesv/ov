<?php

namespace App\Filament\Resources\EntregaCobranzaResource\Pages;

use App\Filament\Resources\EntregaCobranzaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntregaCobranza extends EditRecord
{
    protected static string $resource = EntregaCobranzaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
