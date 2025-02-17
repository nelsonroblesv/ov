<?php

namespace App\Filament\App\Resources\BitacoraProspeccionUserResource\Pages;

use App\Filament\App\Resources\BitacoraProspeccionUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBitacoraProspeccionUsers extends ListRecords
{
    protected static string $resource = BitacoraProspeccionUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
