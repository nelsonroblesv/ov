<?php

namespace App\Filament\Resources\MunicipiosResource\Pages;

use App\Filament\Resources\MunicipiosResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMunicipios extends CreateRecord
{
    protected static string $resource = MunicipiosResource::class;
    protected static ?string $title = 'Registrar Nuevo Municipio';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Municipio registrado')
            ->body('Se ha registrado un nuevo Municipio de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}