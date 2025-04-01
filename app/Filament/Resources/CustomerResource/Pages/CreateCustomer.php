<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected static ?string $title = 'Registrar nuevo Cliente';
    

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Cliente registrado')
            ->body('Se ha registrado un nuevo Cliente de forma exitosa.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->color('success');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $recipient = User::where('role', 'Administrador')->get();
        $assignTo = User::find($data['user_id'])->name;
        $username =  auth()->user()->name;
        $tipo_cliente = $data['tipo_cliente'];
        $data['name'] = ucwords(strtolower($data['name'])); // Convierte a minúsculas y luego pone mayúsculas iniciales
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
            ->body("El usuario ". $username." ha registrado a {$data['name']} 
                        como nuevo Cliente {$cliente} y fue asignado a ". $assignTo)
            ->icon('heroicon-o-information-circle')
            ->iconColor('info')
            ->color('info')
            ->sendToDatabase($recipient);
        return $data;
    }
}
