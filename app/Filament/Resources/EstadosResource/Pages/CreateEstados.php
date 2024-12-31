<?php

namespace App\Filament\Resources\EstadosResource\Pages;

use App\Filament\Resources\EstadosResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEstados extends CreateRecord
{
    protected static string $resource = EstadosResource::class;
    protected static ?string $title = 'Registrar Nuevo Estado';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Estado registrado')
            ->body('Se ha registrado un nuevo Estado de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}