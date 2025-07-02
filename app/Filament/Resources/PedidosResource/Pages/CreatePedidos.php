<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePedidos extends CreateRecord
{
    protected static string $resource = PedidosResource::class;
    protected static ?string $title = 'Nuevo Pedido';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pedido registrado')
            ->body('Se ha registrado una nueva Pedido de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function getSaveFormAction(): Action
       {
           return parent::getSaveFormAction()
               ->label('Update User')
               ->icon('heroicon-o-check-circle')
               ->color('success');
       }

       protected function getCancelFormAction(): Action
       {
           return parent::getCancelFormAction()
               ->label('Cancelar')
               ->icon('heroicon-o-x-mark')
               ->color('gray');
       }
}
