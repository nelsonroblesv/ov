<?php

namespace App\Filament\Resources\RegionesResource\Pages;

use App\Filament\Resources\RegionesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegiones extends ListRecords
{
    protected static string $resource = RegionesResource::class;
    protected static ?string $title = 'Regiones';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nueva Región'),
        ];
    }
}