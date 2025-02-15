<?php

namespace App\Filament\Resources\ZonasResource\Pages;

use App\Filament\Resources\ZonasResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateZonas extends CreateRecord
{
    protected static string $resource = ZonasResource::class;

    protected static ?string $title = 'Registrar Zona';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Zona registrada')
            ->body('Se ha registrado una Zona de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}