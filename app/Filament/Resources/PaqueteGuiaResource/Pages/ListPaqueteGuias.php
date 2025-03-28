<?php

namespace App\Filament\Resources\PaqueteGuiaResource\Pages;

use App\Filament\Resources\PaqueteGuiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaqueteGuias extends ListRecords
{
    protected static string $resource = PaqueteGuiaResource::class;
    protected static ?string $title = 'Paquetes de Guías';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Crear Paquete de Guías')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('success')
        ];
    }
}
