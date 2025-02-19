<?php

namespace App\Filament\App\Resources\BitacoraCustomersResource\Pages;

use App\Filament\App\Resources\BitacoraCustomersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBitacoraCustomers extends ListRecords
{
    protected static string $resource = BitacoraCustomersResource::class;
    protected static ?string $title = 'Registros de Bitacora';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear registro en Bitacora'),
        ];
    }
}