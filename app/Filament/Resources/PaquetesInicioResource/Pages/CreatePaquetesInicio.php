<?php

namespace App\Filament\Resources\PaquetesInicioResource\Pages;

use App\Filament\Resources\PaquetesInicioResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePaquetesInicio extends CreateRecord
{
    protected static string $resource = PaquetesInicioResource::class;

    protected static ?string $title = 'Registrar nuevo Cliente';
    

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Paquete de Inicio registrado')
            ->body('Se ha registrado un nuevo Paquete de Inicio de forma exitosa.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->color('success');
    }
}
