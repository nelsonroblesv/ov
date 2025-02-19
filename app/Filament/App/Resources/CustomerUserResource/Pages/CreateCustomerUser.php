<?php

namespace App\Filament\App\Resources\CustomerUserResource\Pages;

use App\Filament\App\Resources\CustomerUserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerUser extends CreateRecord
{
    protected static string $resource = CustomerUserResource::class;
    protected static ?string $title = 'Registrar Cliente';
    

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cliente registrado')
            ->body('Se ha registrado un nuevo Cliente de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}