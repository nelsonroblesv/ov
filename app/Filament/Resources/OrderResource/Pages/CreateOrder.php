<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Nuevo Pedido';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pedido registrado')
            ->body('Se ha registrado un nuevo Pedido, ahora ya puedes agregar productos.')
            ->icon('heroicon-o-check')
            ->iconColor('info')
            ->color('info');
    }
}
