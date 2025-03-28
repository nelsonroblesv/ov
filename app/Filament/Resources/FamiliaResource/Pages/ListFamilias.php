<?php

namespace App\Filament\Resources\FamiliaResource\Pages;

use App\Filament\Resources\FamiliaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFamilias extends ListRecords
{
    protected static string $resource = FamiliaResource::class;
    protected static ?string $title = 'Familias de Productos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Familia'),
        ];
    }
}

