<?php

namespace App\Filament\App\Resources\RutasResource\Pages;

use App\Filament\App\Resources\RutasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRutas extends ListRecords
{
    protected static string $resource = RutasResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }
}
