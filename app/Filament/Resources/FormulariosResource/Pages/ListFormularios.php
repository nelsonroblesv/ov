<?php

namespace App\Filament\Resources\FormulariosResource\Pages;

use App\Filament\Resources\FormulariosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormularios extends ListRecords
{
    protected static string $resource = FormulariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
