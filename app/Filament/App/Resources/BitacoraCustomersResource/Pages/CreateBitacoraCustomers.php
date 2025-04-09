<?php

namespace App\Filament\App\Resources\BitacoraCustomersResource\Pages;

use App\Filament\App\Resources\BitacoraCustomersResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBitacoraCustomers extends CreateRecord
{
    protected static string $resource = BitacoraCustomersResource::class;
    protected static ?string $title = 'Registrar en Bitacora';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Registro en Bitacora agregado')
            ->body('Se ha agregado un nuevo registo de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}
