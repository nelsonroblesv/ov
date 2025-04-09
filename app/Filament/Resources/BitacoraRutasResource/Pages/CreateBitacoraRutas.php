<?php

namespace App\Filament\Resources\BitacoraRutasResource\Pages;

use App\Filament\Resources\BitacoraRutasResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBitacoraRutas extends CreateRecord
{
    protected static string $resource = BitacoraRutasResource::class;
    protected static ?string $title = 'Regitrar en Bitacora de Prospección';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Registro en Bitacora de Prospeccion agregado')
            ->body('Se ha agregado un nuevo registo de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}
