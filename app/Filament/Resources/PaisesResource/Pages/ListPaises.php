<?php

namespace App\Filament\Resources\PaisesResource\Pages;

use App\Filament\Resources\PaisesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaises extends ListRecords
{
    protected static string $resource = PaisesResource::class;
    protected static ?string $title = 'Paises';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Pais'),
        ];
    }
}
