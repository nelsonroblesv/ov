<?php

namespace App\Filament\Resources\BitacoraProspeccionResource\Pages;

use App\Filament\Resources\BitacoraProspeccionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBitacoraProspeccion extends CreateRecord
{
    protected static string $resource = BitacoraProspeccionResource::class;
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