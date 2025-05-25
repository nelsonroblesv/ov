<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\Pages;

use App\Filament\Resources\EntregaCobranzaManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntregaCobranzaManagers extends ListRecords
{
    protected static string $resource = EntregaCobranzaManagerResource::class;
        protected static ?string $title = 'Administrar Entregas y Cobranzas';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Programar Entrega y Cobranza')
                ->icon('heroicon-o-calendar'),
        ];
    }
}
