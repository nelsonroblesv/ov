<?php

namespace App\Filament\App\Resources\CustomerUserResource\Pages;

use App\Filament\App\Resources\CustomerUserResource;
use App\Models\ClientesPaquetesInicio;
use App\Models\Customer;
use App\Models\GestionRutas;
use App\Models\PaquetesInicio;
use App\Models\User;
use App\Models\Zonas;
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
        $data['created_at'] = Carbon::now()->setTimezone('America/Merida');

        return $data;
    }

    protected function afterCreate(): void
    {
        $customer = $this->record;
        $regionesId = $customer->regiones_id;
        $zonasId = $customer->zonas_id;

        $zonas = Zonas::find($zonasId);
        $tipoSemana = $zonas->tipo_semana;
        $diaSemana = $zonas->dia_zona;

        if ($customer) {
            GestionRutas::insert([
                'user_id'     => auth()->id(),
                'region_id'   => $regionesId,
                'zona_id'     => $zonasId,
                'tipo_semana' => $tipoSemana,
                'dia_semana'  => $diaSemana,
                'customer_id' => $customer->id,
                'created_at'  => now('America/Merida'),
                'updated_at'  => now('America/Merida'),
            ]);
        }

        $recipient = User::where('role', 'Administrador')->get();
        $username = auth()->user()?->name ?? 'Usuario';
        $tipoCliente = $customer->tipo_cliente;
        $tipos = [
            'PV' => 'Punto de Venta',
            'RD' => 'Red',
            'BK' => 'Black',
            'SL' => 'Silver',
            'PO' => 'Posible',
            'PR' => 'Prospecto'
        ];
        $clienteTipoTexto = $tipos[$tipoCliente] ?? 'Desconocido';

        Notification::make()
            ->title('Nuevo Cliente Registrado')
            ->body("El usuario {$username} ha registrado a {$customer->name} como nuevo Cliente {$clienteTipoTexto}. Se ha agregado a la Ruta del usuario.")
            ->icon('heroicon-o-information-circle')
            ->iconColor('info')
            ->color('info')
            ->sendToDatabase($recipient);
    }
}
