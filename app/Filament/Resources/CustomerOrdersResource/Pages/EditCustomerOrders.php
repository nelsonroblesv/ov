<?php

namespace App\Filament\Resources\CustomerOrdersResource\Pages;

use App\Filament\Resources\CustomerOrdersResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCustomerOrders extends EditRecord
{
    protected static string $resource = CustomerOrdersResource::class;
     protected static ?string $title = 'Editar Pedidos del Cliente';

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cambios realizados')
            ->body('Los cambios han sido actualizados correctamente.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }
}
