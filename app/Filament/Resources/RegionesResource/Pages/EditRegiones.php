<?php

namespace App\Filament\Resources\RegionesResource\Pages;

use App\Filament\Resources\RegionesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegiones extends EditRecord
{
    protected static string $resource = RegionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
