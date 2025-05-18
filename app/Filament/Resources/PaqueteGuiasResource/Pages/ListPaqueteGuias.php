<?php

namespace App\Filament\Resources\PaqueteGuiasResource\Pages;

use App\Filament\Resources\PaqueteGuiasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaqueteGuias extends ListRecords
{
    protected static string $resource = PaqueteGuiasResource::class;
    protected static ?string $title = 'Paquetes de Guías';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Paquete de Guías')
                ->icon('heroicon-o-archive-box')
                ->color('success'),
        ];
    }
}
