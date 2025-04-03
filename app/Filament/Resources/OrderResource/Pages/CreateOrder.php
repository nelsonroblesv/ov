<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\IconPosition;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Nuevo Pedido';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    // Customise the "Create" button
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Registrar Nuevo Pedido')
            ->icon('heroicon-o-shopping-bag')
            ->iconPosition(IconPosition::Before)
            ->color('success');;
    }

    // Customise the "Create & Create Another" button
    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            //->label('Save & Create Another')
            //->icon('heroicon-o-plus-circle')
            //->iconPosition(IconPosition::Before);
            ->hidden();
    }

    // Customise the "Cancel" button
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Regresar')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Nuevo Pedido Registrado')
            ->body('Se ha registrado un Nuevo Pedido, ahora ya puedes agregar Productos.')
            ->icon('heroicon-o-shopping-bag')
            ->iconColor('info')
            ->color('info');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Obtener el cliente
        $customerId = Customer::find($data['customer_id']);
        $customer = $customerId?->name ?? 'Cliente desconocido';

        // Obtener IDs de los usuarios involucrados
        $solicita = is_array($data['solicitado_por']) ? $data['solicitado_por'] : [$data['solicitado_por']];
        $registra = $data['registrado_por'] ?? null;
        $usuarioLogueadoId = auth()->id(); // ID del usuario autenticado

        // Obtener usuarios con rol "Administrador"
        $adminUsers = User::where('role', 'Administrador')->get();

        // Obtener los usuarios solicitantes y quien registró la orden
        $vendedores = User::whereIn('id', $solicita)->get();
        $registrador = $registra ? User::find($registra) : null;

        // Unir administradores, vendedores y el usuario que registró la orden
        $destinos = $adminUsers->merge($vendedores);

        // Evitar que el usuario logueado reciba dos veces la notificación
        if ($registrador && !$adminUsers->contains($registrador) && $registrador->id !== $usuarioLogueadoId) {
            $destinos->push($registrador);
        }

        // Filtrar duplicados
        $destinos = $destinos->unique('id');

        // Asignar fecha de creación
        $data['created_at'] = Carbon::now()->setTimezone('America/Merida');
        $data['updated_at'] = null;
        $data['deleted_at'] = null;

        // Mapeo de estados
        $estados = [
            'PEN' => 'PENDIENTE',
            'COM' => 'COMPLETADO',
            'REC' => 'RECHAZADO',
            'REU' => 'REUBICADO',
            'DEV' => 'DEVUELTA PARCIAL',
            'SIG' => 'SIGUIENTE VISITA',
        ];
        $estado = $estados[$data['status']] ?? 'DESCONOCIDO';

        // Construir la lista de vendedores
        $nombresVendedores = $vendedores->pluck('name')->implode(', ');

        // Enviar la notificación
        if ($destinos->isNotEmpty()) {
            Notification::make()
                ->title('Nuevo Pedido Registrado')
                ->body(($registrador?->name ?? 'Alguien') .
                    ' agregó un Nuevo Pedido de ' . ($nombresVendedores ?: 'Desconocido') .
                    ' para: ' . $customer . '. Estado: ' . $estado)
                ->icon('heroicon-o-shopping-bag')
                ->iconColor('info')
                ->color('info')
                ->sendToDatabase($destinos);
        }

        return $data;
    }
}
