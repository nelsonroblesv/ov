<?php

namespace App\Filament\Resources\ColoniasResource\Pages;

use App\Filament\Resources\ColoniasResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateColonias extends CreateRecord
{
    protected static string $resource = ColoniasResource::class;
    protected static ?string $title = 'Registrar Nueva Colonia';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Colonia registrada')
            ->body('Se ha registrado una nueva Colonia de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}