<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\Pages;

use App\Filament\Resources\EntregaCobranzaManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntregaCobranzaManager extends EditRecord
{
    protected static string $resource = EntregaCobranzaManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
