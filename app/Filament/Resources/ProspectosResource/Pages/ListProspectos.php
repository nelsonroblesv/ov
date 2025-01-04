<?php

namespace App\Filament\Resources\ProspectosResource\Pages;

use App\Filament\Resources\ProspectosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProspectos extends ListRecords
{
    protected static string $resource = ProspectosResource::class;
    protected static ?string $title = 'Prospectos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Prospecto'),
        ];
    }
}