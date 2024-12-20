<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\IconPosition;

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
        $recipient = auth()->user();

        return Notification::make()
            ->success()
            ->title('Pedido registrado')
            ->body('Se ha registrado un nuevo Pedido, ahora ya puedes agregar productos.')
            ->icon('heroicon-o-check')
            ->iconColor('info')
            ->color('info');
    }

    // Customise the "Create" button
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Registrar Pedido')
            ->icon('heroicon-o-shopping-bag')
            ->iconPosition(IconPosition::Before)
            ->color('success');;
    }

   // Customise the "Create & Create Another" button
   protected function getCreateAnotherFormAction(): Action
   {
       return parent::getCreateAnotherFormAction()
           //->label('Save & Create Another')
           //->icon('heroicon-o-plus-circle')
           //->iconPosition(IconPosition::Before);
           ->hidden();
   }

   // Customise the "Cancel" button
   protected function getCancelFormAction(): Action
   {
       return parent::getCancelFormAction()
           ->label('Regresar')
           ->icon('heroicon-o-arrow-uturn-left')
           ->color('gray');
   }

   protected function mutateFormDataBeforeCreate(array $data): array
   {
    
    $recipient = auth()->user();

    Notification::make()
        ->title('Nuevo Pedido')
       ->body("**Se ha registrado un nuevo Pedido**")
        ->sendToDatabase($recipient);
        return $data;
   }
}
