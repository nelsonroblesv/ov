<?php

namespace App\Filament\Resources\ZoneResource\Pages;

use App\Filament\Resources\ZoneResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateZone extends CreateRecord
{
    protected static string $resource = ZoneResource::class;
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
            ->body('Se ha registrado una nueva Zona de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

/* For TESTING

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data); // Detiene la ejecución y muestra los datos del formulario
        return $data;
    }
       
    
    */
}
