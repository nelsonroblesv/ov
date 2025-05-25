<?php

namespace App\Filament\Resources\EntregaCobranzaResource\Pages;

use App\Filament\Resources\EntregaCobranzaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntregaCobranzas extends ListRecords
{
    protected static string $resource = EntregaCobranzaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
