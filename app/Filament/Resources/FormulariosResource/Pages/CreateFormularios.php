<?php

namespace App\Filament\Resources\FormulariosResource\Pages;

use App\Filament\Resources\FormulariosResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFormularios extends CreateRecord
{
    protected static string $resource = FormulariosResource::class;
    protected static ?string $title = 'Nuevo Registro a Evento';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Registro a Evento registrado')
            ->body('Se ha creado un nuevo Registro a Evento de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('Nuevo Registro')
            ->body("**Se ha creado un nuevo registro**")
            ->sendToDatabase($recipient);
        return $data;
    }
}
