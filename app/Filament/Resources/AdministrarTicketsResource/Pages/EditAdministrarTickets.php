<?php

namespace App\Filament\Resources\AdministrarTicketsResource\Pages;

use App\Filament\Resources\AdministrarTicketsResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use PHPUnit\Framework\Attributes\Ticket;

class EditAdministrarTickets extends EditRecord
{
    protected static string $resource = AdministrarTicketsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $ticket = $this->record;

        if ($ticket->estado !== $data['estado']) {
            $this->notifyStatusChange($ticket, $data['estado']);
            $data['updated_at'] = Carbon::now()->setTimezone('America/Merida');

        }

        return $data;
    }

    private function notifyStatusChange(Ticket $number, $newStatus)
    {
        $ticket = Ticket::find($number->customer_id)?->name;
        
        $solicita = is_array($order->solicitado_por) ? $order->solicitado_por : [$order->solicitado_por];
        $registra = $order->registrado_por;
        $usuarioLogueadoId = auth()->id(); // ID del usuario autenticado

        // Obtener usuarios con rol "Administrador"
        $adminUsers = User::where('role', 'Administrador')->get();

        // Obtener los usuarios solicitantes y quien registró la orden
        $vendedores = User::whereIn('id', $solicita)->get();
        $registrador = $registra ? User::find($registra) : null;

        // Unir administradores, vendedores y registrador
        $destinos = $adminUsers->merge($vendedores);

        // Evitar duplicados y que el usuario logueado reciba la notificación dos veces
        if ($registrador && !$adminUsers->contains($registrador) && $registrador->id !== $usuarioLogueadoId) {
            $destinos->push($registrador);
        }

        // Filtrar duplicados
        $destinos = $destinos->unique('id');

        // Mapeo de estados
        $estados = [
            'PEN' => 'PENDIENTE',
            'COM' => 'COMPLETADO',
            'REC' => 'RECHAZADO',
            'REU' => 'REUBICADO',
            'DEV' => 'DEVUELTA PARCIAL',
            'SIG' => 'SIGUIENTE VISITA',
        ];
        $estado = $estados[$newStatus] ?? 'DESCONOCIDO';

        // Construir la lista de vendedores
        $nombresVendedores = $vendedores->pluck('name')->implode(', ');

        // Enviar la notificación
        if ($destinos->isNotEmpty()) {
            Notification::make()
                ->title('Ticket Cerrado')
                ->body("El ticket solicitado por {$nombresVendedores} ha sido resuelto.: {$cliente}, 
                            ha cambiado su estado a:  **{$estado}**.")
                ->icon('heroicon-o-shopping-bag')
                ->iconColor('info')
                ->color('info')
                ->sendToDatabase($destinos);
        }
    }
}
