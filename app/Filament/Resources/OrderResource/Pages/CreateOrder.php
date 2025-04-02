<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use App\Models\User;
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

    // Customise the "Create" button
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Registrar Nuevo Pedido')
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

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Nuevo Pedido Registrado')
            ->body('Se ha registrado un Nuevo Pedido, ahora ya puedes agregar Productos.')
            ->icon('heroicon-o-shopping-bag')
            ->iconColor('info')
            ->color('info');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Obtener el usuario del customer
        $customerId = Customer::find($data['customer_id']);
        $customer = $customerId->name;

        $customerUserId = Customer::where('id', $data['customer_id'])->value('user_id');

        // Obtener los usuarios con rol "Administrador"
        $adminUsers = User::where('role', 'Administrador')->get();

        $customerUser = $customerUserId ? User::find($customerUserId) : null;

        $users = $adminUsers->when($customerUser, function ($collection) use ($customerUser) {
            return $collection->push($customerUser);
        });
        
        $addBy =  auth()->user()->name;
        if ($users->isNotEmpty()) {
            Notification::make()
                ->title('Nuevo Pedido Registrado')
                ->body($addBy . ' agregó un Nuevo Pedido para: ' . $customer.'. Estado: PENDIENTE.')
                ->icon('heroicon-o-information-circle')
                ->iconColor('info')
                ->color('info')
                ->sendToDatabase($users);
        }

        return $data;
    }
}
