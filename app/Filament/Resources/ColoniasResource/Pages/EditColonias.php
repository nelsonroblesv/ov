<?php

namespace App\Filament\Resources\ColoniasResource\Pages;

use App\Filament\Resources\ColoniasResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditColonias extends EditRecord
{
    protected static string $resource = ColoniasResource::class;

    protected static ?string $title = 'Editar Colonia';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cambios realizados')
            ->body('Se ha actualizado el registro correctamente.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Borrar'),
        ];
    }

}
