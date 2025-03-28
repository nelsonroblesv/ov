<?php

namespace App\Filament\Resources\PaqueteGuiaResource\Pages;

use App\Filament\Resources\PaqueteGuiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaqueteGuias extends ListRecords
{
    protected static string $resource = PaqueteGuiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
