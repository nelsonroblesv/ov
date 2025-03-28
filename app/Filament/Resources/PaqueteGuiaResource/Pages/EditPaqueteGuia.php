<?php

namespace App\Filament\Resources\PaqueteGuiaResource\Pages;

use App\Filament\Resources\PaqueteGuiaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaqueteGuia extends EditRecord
{
    protected static string $resource = PaqueteGuiaResource::class;
    protected static ?string $title = 'Editar Paquete de Guías';

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