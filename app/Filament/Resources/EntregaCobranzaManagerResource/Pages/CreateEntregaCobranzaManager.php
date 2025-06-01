<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\Pages;

use App\Filament\Resources\EntregaCobranzaManagerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEntregaCobranzaManager extends CreateRecord
{
    protected static string $resource = EntregaCobranzaManagerResource::class;
    protected static ?string $title = 'Nuevo Periodo de Visitas';

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Periodo de Visitas Registrado')
            ->body('Se ha creado un nuevo Periodo de Visitas. Ahora puedes agregar Visitas.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->color('success');
    }
}
