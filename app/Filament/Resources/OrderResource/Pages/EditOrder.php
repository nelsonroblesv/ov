<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
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
            ->label('Regresar')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Obtener la orden original antes de la actualización
        $order = $this->record;

        // Verificar si el estado cambió
        if ($order->status !== $data['status']) {
            $this->notifyStatusChange($order, $data['status']);
        }

        return $data;
    }

    private function notifyStatusChange(Order $order, $newStatus)
    {
        // Obtener el usuario del customer asignado a la orden
        $customerId = Customer::find($order->customer_id);
        $customer = $customerId->name;

        $customerUserId = Customer::where('id', $order->customer_id)->value('user_id');
        $vendedor = User::where('id', $customerUserId)->value('name');

        // Obtener usuarios con rol "Administrador"
        $adminUsers = User::where('role', 'Administrador')->get();
        $customerUser = $customerUserId ? User::find($customerUserId) : null;

        // Unir los administradores con el usuario del customer
        $users = $adminUsers->when($customerUser, function ($collection) use ($customerUser) {
            return $collection->push($customerUser);
        });

        // Si hay usuarios, enviar la notificación
        $addBy =  auth()->user()->name;
        if ($users->isNotEmpty()) {
            Notification::make()
                ->title('Pedido Actualizado')
                ->body($addBy . ' cambio el Pedido de '.$vendedor.' para: ' . $customer.'. Estado: '.$newStatus)
                ->icon('heroicon-o-information-circle')
                ->iconColor('info')
                ->color('info')
                ->sendToDatabase($users);
        }

    }

}
