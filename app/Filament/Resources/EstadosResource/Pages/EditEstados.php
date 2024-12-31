<?php

namespace App\Filament\Resources\EstadosResource\Pages;

use App\Filament\Resources\EstadosResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEstados extends EditRecord
{
    protected static string $resource = EstadosResource::class;

    protected static ?string $title = 'Editar Estado';

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
