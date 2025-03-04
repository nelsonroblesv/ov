<?php

namespace App\Filament\Resources\AsignarTipoSemanaResource\Pages;

use App\Filament\Resources\AsignarTipoSemanaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsignarTipoSemana extends EditRecord
{
    protected static string $resource = AsignarTipoSemanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\DeleteAction::make(),
        ];
    }
}
