<?php

namespace App\Filament\App\Resources\ProspectosResource\Pages;

use App\Filament\App\Resources\ProspectosResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProspectos extends CreateRecord
{
    protected static string $resource = ProspectosResource::class;
    protected static ?string $title = 'Registrar Prospecto';
    

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Prospeccion registrada')
            ->body('Se ha registrado una nueva Prospeccion de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}