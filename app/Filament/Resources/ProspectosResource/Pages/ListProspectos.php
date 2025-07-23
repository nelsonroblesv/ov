<?php

namespace App\Filament\Resources\ProspectosResource\Pages;

use App\Filament\Resources\ProspectosResource;
use App\Filament\Resources\ProspectosResource\Widgets\ProspectosMapWidget;
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
                ->label('Nueva Prospección')
                ->icon('heroicon-o-magnifying-glass-plus')
                ->color('warning'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
          //ProspectosMapWidget::class,
        ];
    }
}
