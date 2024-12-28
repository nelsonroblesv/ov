<?php

namespace App\Filament\Resources\PreferredModuleResource\Pages;

use App\Filament\Resources\PreferredModuleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePreferredModule extends CreateRecord
{
    protected static string $resource = PreferredModuleResource::class;
    protected static ?string $title = 'Nuevo Modulo Preferred';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Modulo registrado')
            ->body('Se ha registrado un nuevo Modulo Preferred de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}
