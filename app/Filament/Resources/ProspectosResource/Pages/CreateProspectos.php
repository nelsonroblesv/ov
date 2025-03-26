<?php

namespace App\Filament\Resources\ProspectosResource\Pages;

use App\Filament\Resources\ProspectosResource;
use App\Models\User;
use Filament\Actions;
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
            ->title('Prospección registrada')
            ->body('Se ha registrado una nueva Prospección de forma exitosa.')
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
        $tipos = [
            'PO' => 'Posible',
            'PR' => 'Prospecto'        
        ];
        $cliente = $tipos[$tipo_cliente];

        Notification::make()
            ->title('Nueva Prospección Registrada')
            ->body("El usuario ". $username." ha registrado a {$data['name']} 
                        como {$cliente} y fue asignado a ". $assignTo)
            ->icon('heroicon-o-information-circle')
            ->iconColor('info')
            ->color('info')
            ->sendToDatabase($recipient);
        return $data;
    }
}

