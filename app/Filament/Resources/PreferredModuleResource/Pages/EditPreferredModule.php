<?php

namespace App\Filament\Resources\PreferredModuleResource\Pages;

use App\Filament\Resources\PreferredModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPreferredModule extends EditRecord
{
    protected static string $resource = PreferredModuleResource::class;
    protected static ?string $title = 'Editar Modulo Preferred';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
