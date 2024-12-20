<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Editar Pedido';

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

    // Customize the "Save" button
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Actualizar Pedido')
            ->icon('heroicon-o-check-circle')
            ->iconPosition(IconPosition::Before)
            ->color('success');
    }

    // Customize the "Cancel" button
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar')
            ->icon('heroicon-o-x-mark')
            ->color('gray');
    }

}
