<?php

namespace App\Filament\App\Resources\CustomerUserResource\Pages;

use App\Filament\App\Resources\CustomerUserResource;
use App\Models\ClientesPaquetesInicio;
use App\Models\Customer;
use App\Models\PaquetesInicio;
use App\Models\User;
use Carbon\Carbon;
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

        $recipient = User::where('role', 'Administrador')->get();
        $username =  User::find($data['user_id'])->name;
        $tipo_cliente = $data['tipo_cliente'];
        //$data['name'] = ucwords(strtolower($data['name'])); // Convierte a minúsculas y luego pone mayúsculas iniciales
        $tipos = [
            'PV' => 'Punto de Venta',
            'RD' => 'Red',
            'BK' => 'Black',
            'SL' => 'Silver',
            'PO' => 'Posible',
            'PR' => 'Prospecto'
        ];
        $cliente = $tipos[$tipo_cliente];
        $data['created_at'] = Carbon::now()->setTimezone('America/Merida');

        Notification::make()
            ->title('Nuevo Cliente Registrado')
            ->body("El usuario " . $username . " ha registrado a {$data['name']} como nuevo Cliente {$cliente}.")
            ->icon('heroicon-o-information-circle')
            ->iconColor('info')
            ->color('info')
            ->sendToDatabase($recipient);
        return $data;
    }
}
