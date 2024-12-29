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
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Modulo registrado')
            ->body('Se ha registrado un nuevo Modulo, ahora ya puedes agregar productos.')
            ->icon('heroicon-o-check')
            ->iconColor('info')
            ->color('info');
    }
}
