<?php

namespace App\Filament\Resources\AdministrarTicketsResource\Pages;

use App\Filament\Resources\AdministrarTicketsResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use PHPUnit\Framework\Attributes\Ticket;

class EditAdministrarTickets extends EditRecord
{
    protected static string $resource = AdministrarTicketsResource::class;
    protected static ?string $title = 'Editar Ticket';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Ticket Actualizado')
            ->body('Se ha actualizado el estado del Ticket de forma correcta.')
            ->icon('heroicon-o-ticket')
            ->iconColor('success')
            ->color('success');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $info = $this->record;

        // Verificar si el estado cambió
        if ($info->estado !== $data['estado']) {
            $this->notifyStatusChange($info, $data['estado']);
            $data['updated_at'] = Carbon::now()->setTimezone('America/Merida');
        }
        return $data;
    }

    protected function notifyStatusChange($ticket, string $nuevoEstado): void
    {
        $toUser = User::find($ticket->to_user_id)?->name ?? 'Usuario desconocido';
        $remitente = User::find($ticket->from_user_id);
        
       
        $nuevoEstado = match ($nuevoEstado) {
            '' => 'Abierto',
            '1' => 'Cerrado',
        };

        if ($remitente) {
            Notification::make()
                ->title('Estado del Ticket Actualizado')
                ->body($toUser. " ha cambiado el estado de tu ticket con folio OV-{$ticket->id} 
                            a: {$nuevoEstado}.")
                ->icon('heroicon-o-ticket')
                ->iconColor('success')
                ->color('success')
                ->sendToDatabase($remitente);
        }
    }
}
