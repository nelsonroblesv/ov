<?php

namespace App\Filament\App\Resources\ProspectosResource\Pages;

use App\Filament\App\Resources\ProspectosResource;
use App\Filament\Resources\ProspectosResource\Widgets\MapProspeccionWidget;
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

    protected function getHeaderWidgets(): array
    {
        return [
            MapProspeccionWidget::class,
        ];
    }

       
}
