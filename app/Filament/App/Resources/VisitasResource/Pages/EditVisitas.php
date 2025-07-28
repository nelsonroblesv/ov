<?php

namespace App\Filament\App\Resources\VisitasResource\Pages;

use App\Filament\App\Resources\VisitasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisitas extends EditRecord
{
    protected static string $resource = VisitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\DeleteAction::make(),
        ];
    }
}
