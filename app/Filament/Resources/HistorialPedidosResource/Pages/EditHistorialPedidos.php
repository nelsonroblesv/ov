<?php

namespace App\Filament\Resources\HistorialPedidosResource\Pages;

use App\Filament\Resources\HistorialPedidosResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditHistorialPedidos extends EditRecord
{
    protected static string $resource = HistorialPedidosResource::class;
    protected static ?string $title = 'Editar Pedido (Historico)';

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