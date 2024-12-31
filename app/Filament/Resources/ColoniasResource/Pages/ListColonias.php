<?php

namespace App\Filament\Resources\ColoniasResource\Pages;

use App\Filament\Resources\ColoniasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListColonias extends ListRecords
{
    protected static string $resource = ColoniasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
