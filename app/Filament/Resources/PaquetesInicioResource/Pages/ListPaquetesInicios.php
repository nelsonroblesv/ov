<?php

namespace App\Filament\Resources\PaquetesInicioResource\Pages;

use App\Filament\Resources\PaquetesInicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaquetesInicios extends ListRecords
{
    protected static string $resource = PaquetesInicioResource::class;
    protected static ?string $title = 'Paquetes de Inicio';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Paquete de Inicio')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}