<?php

namespace App\Filament\App\Resources\ProspectosResource\Pages;

use App\Filament\App\Resources\ProspectosResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProspectos extends CreateRecord
{
    protected static string $resource = ProspectosResource::class;
    protected static ?string $title = 'Registrar Prospecto';


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Prospeccion registrada')
            ->body('Se ha registrado una nueva Prospeccion de forma exitosa.')
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
            ->body("El vendedor ". $username." ha registrado a {$data['name']} como {$cliente}.")
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success')
            ->sendToDatabase($recipient);
        return $data;
    }
}
