<?php

namespace App\Filament\Resources\PaquetesInicioResource\Pages;

use App\Filament\Resources\PaquetesInicioResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaquetesInicio extends EditRecord
{
    protected static string $resource = PaquetesInicioResource::class;

    protected static ?string $title = 'Editar Paquete de Inicio';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Cambios realizados')
            ->body('Se ha actualizado el registro correctamente.')
            ->icon('heroicon-o-arrow-path')
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
