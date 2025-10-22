<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use App\Models\Customer;
use App\Models\Pedido;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;

class EditPedidos extends EditRecord
{
    protected static string $resource = PedidosResource::class;
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
            ->iconPosition(IconPosition::Before);
    }

    // Customize the "Cancel" button
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar')
            ->icon('heroicon-o-x-mark');
    }

    protected function beforeSave(): void
    {
        $data = $this->form->getState();
       
        $existe = Pedido::where('customer_id', $data['customer_id'])
            ->where('dia_nota', $data['dia_nota'])
            ->where('tipo_semana_nota', $data['tipo_semana_nota'])
            ->where('periodo', $data['periodo'])
            ->where('semana', $data['semana'])
            ->where('id', '!=', $this->record->id)
            ->exists();

        if ($existe) {
            Notification::make()
                ->title('Registro existente')
                ->body('Este Cliente ya tiene una ruta registrada con la misma combinación de datos. Por favor, verifica los datos ingresados.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt(); // Detiene la actualización
        }
    }
}
