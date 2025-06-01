<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\Pages;

use App\Filament\Resources\EntregaCobranzaManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntregaCobranzaManagers extends ListRecords
{
    protected static string $resource = EntregaCobranzaManagerResource::class;
        protected static ?string $title = 'Agenda de Visitas';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Periodo de Visitas')
                ->icon('heroicon-o-calendar-date-range'),
        ];
    }
}
