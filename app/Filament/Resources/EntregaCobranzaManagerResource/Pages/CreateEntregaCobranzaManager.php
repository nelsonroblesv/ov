<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\Pages;

use App\Filament\Resources\EntregaCobranzaManagerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEntregaCobranzaManager extends CreateRecord
{
    protected static string $resource = EntregaCobranzaManagerResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Entrega/Cobranza Programada')
            ->body('Se ha registrado una nueva Entrega/Cobranza. Ahora puedes agregar Clientes.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->color('success');
    }
}
