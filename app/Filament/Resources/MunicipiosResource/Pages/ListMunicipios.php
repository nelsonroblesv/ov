<?php

namespace App\Filament\Resources\MunicipiosResource\Pages;

use App\Filament\Resources\MunicipiosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMunicipios extends ListRecords
{
    protected static string $resource = MunicipiosResource::class;

    protected static ?string $title = 'Municipios';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nuevo Municipio'),
        ];
    }
}
