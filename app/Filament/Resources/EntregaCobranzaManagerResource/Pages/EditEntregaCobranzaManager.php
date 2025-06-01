<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\Pages;

use App\Filament\Resources\EntregaCobranzaManagerResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditEntregaCobranzaManager extends EditRecord
{
    protected static string $resource = EntregaCobranzaManagerResource::class;
    protected static ?string $title = 'Editar Detalles de la Visita';

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
