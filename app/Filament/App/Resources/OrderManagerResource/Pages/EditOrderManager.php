<?php

namespace App\Filament\App\Resources\OrderManagerResource\Pages;

use App\Filament\App\Resources\OrderManagerResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditOrderManager extends EditRecord
{
    protected static string $resource = OrderManagerResource::class;
    protected static ?string $title = 'Pedidos y Pagos del Cliente';

    protected function getHeaderActions(): array
    {
        return [
           // Actions\DeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->hidden(true);
    }

    // Hide the "Cancel" button
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->hidden(true);
    }
}
