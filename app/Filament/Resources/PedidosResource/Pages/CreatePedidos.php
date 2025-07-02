<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as ActionsAction;
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

    protected function getFormActions(): array
    {
        return [
           $this->getCreateFormAction()
            ->label('Registrar Pedido')
            ->icon('heroicon-o-check'),

           $this->getCreateAnotherFormAction()->label('Guardar y Registrar Otro')
           ->hidden(),
           $this->getCancelFormAction()->label('Cancelar')
            ->icon('heroicon-o-x-mark'),
        ];
    }
}
