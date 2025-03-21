<?php

namespace App\Filament\App\Resources\CustomerUserResource\Pages;

use App\Filament\App\Resources\CustomerUserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerUser extends CreateRecord
{
    protected static string $resource = CustomerUserResource::class;
    protected static ?string $title = 'Registrar Cliente';


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Cliente registrado')
            ->body('Se ha registrado un nuevo Cliente de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $recipient = auth()->user();
        $username =  User::find($data['user_id'])->name;
        $tipo_cliente = $data['tipo_cliente'];
        $tipos = [
            'PV' => 'Punto de Venta',
            'RD' => 'Red',
            'BK' => 'Black',
            'SL' => 'Silver',
            'PO' => 'Posible',
            'PR' => 'Prospecto'        
        ];
        $cliente = $tipos[$tipo_cliente];

        Notification::make()
            ->title('Nuevo Cliente Registrado')
            ->body("El vendedor ". $username." ha registrado a {$data['name']} como nuevo Cliente {$cliente}.")
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success')
            ->sendToDatabase($recipient);
        return $data;
    }
}
