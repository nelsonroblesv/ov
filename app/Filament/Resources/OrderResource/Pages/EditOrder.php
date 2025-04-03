<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Editar Pedido';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cambios realizados')
            ->body('Se ha actualizado el registro correctamente.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Borrar'),
        ];
    }

    // Customize the "Save" button
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Actualizar Pedido')
            ->icon('heroicon-o-check-circle')
            ->iconPosition(IconPosition::Before)
            ->color('success');
    }

    // Customize the "Cancel" button
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Regresar')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Obtener la orden original antes de la actualización
        $order = $this->record;

        // Verificar si el estado cambió
        if ($order->status !== $data['status']) {
            $this->notifyStatusChange($order, $data['status']);
            $data['updated_at'] = Carbon::now()->setTimezone('America/Merida');

        }

        return $data;
    }

    private function notifyStatusChange(Order $order, $newStatus)
    {
        $cliente = Customer::find($order->customer_id)?->name;
        
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
                ->title('Cambio de Estado en Pedido')
                ->body("El pedido de {$nombresVendedores} para el cliente: {$cliente}, 
                            ha cambiado su estado a: **{$estado}**.")
                ->icon('heroicon-o-arrow-path')
                ->iconColor('info')
                ->color('info')
                ->sendToDatabase($destinos);
        }
    }
}
