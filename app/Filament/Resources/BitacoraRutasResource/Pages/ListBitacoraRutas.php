<?php

namespace App\Filament\Resources\BitacoraRutasResource\Pages;

use App\Filament\Resources\BitacoraRutasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBitacoraRutas extends ListRecords
{
    protected static string $resource = BitacoraRutasResource::class;
    protected static ?string $title = 'Registros de Bitacora';

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make()
             // ->label('Crear registro en Bitacora'),
        ];
    }
}