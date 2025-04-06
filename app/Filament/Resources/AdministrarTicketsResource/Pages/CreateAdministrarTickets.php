<?php

namespace App\Filament\Resources\AdministrarTicketsResource\Pages;

use App\Filament\Resources\AdministrarTicketsResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAdministrarTickets extends CreateRecord
{
    protected static string $resource = AdministrarTicketsResource::class;
    protected static ?string $title = 'Registrar Ticket';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Cliente registrado')
            ->body('Se ha registrado el Ticket de forma correcta. Será atendido en breve.')
            ->icon('heroicon-o-ticket')
            ->iconColor('success')
            ->color('success');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $recipient = User::findOrFail($data['to_user_id']);
        $username =  $data['from_user_id'];
       
        $data['created_at'] = Carbon::now()->setTimezone('America/Merida');
        $data['updated_at'] = null;

        Notification::make()
            ->title('Nuevo Ticket Abierto')
            ->body("El usuario " . $username . " ha abierto un nuevo Ticket.")
            ->icon('heroicon-o-ticket')
            ->iconColor('info')
            ->color('info')
            ->sendToDatabase($recipient);
        return $data;
    }

}
