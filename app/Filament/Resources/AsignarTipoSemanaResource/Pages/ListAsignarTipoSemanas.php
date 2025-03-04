<?php

namespace App\Filament\Resources\AsignarTipoSemanaResource\Pages;

use App\Filament\Resources\AsignarTipoSemanaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsignarTipoSemanas extends ListRecords
{
    protected static string $resource = AsignarTipoSemanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }
}
