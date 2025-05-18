<?php

namespace App\Filament\Resources\PaqueteGuiasResource\Pages;

use App\Filament\Resources\PaqueteGuiasResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaqueteGuias extends EditRecord
{
    protected static string $resource = PaqueteGuiasResource::class;

    protected static ?string $title = 'Editar Paquete de Guías';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Paquete de Guías actualizado')
            ->body('Se ha actualizado el registro correctamente.')
            ->icon('heroicon-o-archive-box')
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
