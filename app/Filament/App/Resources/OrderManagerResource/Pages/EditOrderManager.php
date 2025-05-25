<?php

namespace App\Filament\App\Resources\OrderManagerResource\Pages;

use App\Filament\App\Resources\OrderManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderManager extends EditRecord
{
    protected static string $resource = OrderManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
