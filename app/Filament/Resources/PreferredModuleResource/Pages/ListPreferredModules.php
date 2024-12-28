<?php

namespace App\Filament\Resources\PreferredModuleResource\Pages;

use App\Filament\Resources\PreferredModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPreferredModules extends ListRecords
{
    protected static string $resource = PreferredModuleResource::class;
    protected static ?string $title = 'Modulos Preferred';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Modulo'),
        ];
    }
}
