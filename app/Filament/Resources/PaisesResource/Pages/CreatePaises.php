<?php

namespace App\Filament\Resources\PaisesResource\Pages;

use App\Filament\Resources\PaisesResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePaises extends CreateRecord
{
    protected static string $resource = PaisesResource::class;
    protected static ?string $title = 'Nuevo Pais';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pais registrado')
            ->body('Se ha registrado un nuevo Pais de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}
