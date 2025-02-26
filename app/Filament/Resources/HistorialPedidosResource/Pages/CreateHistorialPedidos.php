<?php

namespace App\Filament\Resources\HistorialPedidosResource\Pages;

use App\Filament\Resources\HistorialPedidosResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateHistorialPedidos extends CreateRecord
{
    protected static string $resource = HistorialPedidosResource::class;
    protected static ?string $title = 'Nuevo Pedido (Historico)';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pedido (Historico) registrado')
            ->body('Se ha registrado una nueva Pedido de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}