<?php

namespace App\Filament\Resources\PaqueteGuiasResource\Pages;

use App\Filament\Resources\PaqueteGuiasResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePaqueteGuias extends CreateRecord
{
    protected static string $resource = PaqueteGuiasResource::class;
    protected static ?string $title = 'Registrar Nuevo Paquete de Guias';


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Paquete de Guias registrado')
            ->body('Se ha registrado un nuevo Paquete de Guias de forma exitosa. Ya puedes agregar guias a este paquete.')
            ->icon('heroicon-o-archive-box')
            ->iconColor('success')
            ->color('success');
    }
}
