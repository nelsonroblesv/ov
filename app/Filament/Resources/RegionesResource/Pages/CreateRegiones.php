<?php

namespace App\Filament\Resources\RegionesResource\Pages;

use App\Filament\Resources\RegionesResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateRegiones extends CreateRecord
{
    protected static string $resource = RegionesResource::class;
    
    protected static ?string $title = 'Registrar Región';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Región registrada')
            ->body('Se ha registrado una nueva Región de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}
