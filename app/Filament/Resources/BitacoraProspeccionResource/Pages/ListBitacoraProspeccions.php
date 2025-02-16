<?php

namespace App\Filament\Resources\BitacoraProspeccionResource\Pages;

use App\Filament\Resources\BitacoraProspeccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBitacoraProspeccions extends ListRecords
{
    protected static string $resource = BitacoraProspeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
