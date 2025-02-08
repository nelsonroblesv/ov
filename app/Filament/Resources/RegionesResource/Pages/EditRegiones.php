<?php

namespace App\Filament\Resources\RegionesResource\Pages;

use App\Filament\Resources\RegionesResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRegiones extends EditRecord
{
    protected static string $resource = RegionesResource::class;

protected static ?string $title = 'Editar Región';

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