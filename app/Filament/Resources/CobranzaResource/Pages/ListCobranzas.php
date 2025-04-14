<?php

namespace App\Filament\Resources\CobranzaResource\Pages;

use App\Filament\Resources\CobranzaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCobranzas extends ListRecords
{
    protected static string $resource = CobranzaResource::class;
    protected static ?string $title = 'Vista Deuda';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Registrar Deuda'),
        ];
    }
}
