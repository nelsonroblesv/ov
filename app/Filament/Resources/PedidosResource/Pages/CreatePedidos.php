<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use App\Models\Customer;
use App\Models\Pedido;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePedidos extends CreateRecord
{
    protected static string $resource = PedidosResource::class;
    protected static ?string $title = 'Nuevo Pedido';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pedido registrado')
            ->body('Se ha registrado un nuevo Pedido de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Registrar Pedido')
                ->icon('heroicon-o-check'),

            $this->getCreateAnotherFormAction()->label('Guardar y Registrar Otro')
                ->hidden(),
            $this->getCancelFormAction()->label('Cancelar')
                ->icon('heroicon-o-x-mark'),
        ];
    }
    /*
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
*/
    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        $existe = Pedido::where('customer_id', $data['customer_id'])
            ->where('dia_nota', $data['dia_nota'])
            ->where('tipo_semana_nota', $data['tipo_semana_nota'])
            ->where('periodo', $data['periodo'])
            ->where('semana', $data['semana'])
            ->exists();

        if ($existe) {
            Notification::make()
                ->title('Registro existente')
                ->body('Este Cliente ya tiene una ruta registrada con la misma combinación de datos. Por favor, verifica los datos ingresados.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt(); // Detiene el guardado
        }
    }

    protected function fillForm(): void
    {
        $customerIdFromUrl = request()->query('customer_id');
        $data = []; // Array para los datos a inyectar

        if ($customerIdFromUrl) {

            $customer = Customer::with(['zona', 'regiones'])->find($customerIdFromUrl);

            if ($customer) {
                // 1. Establecer el valor del Select (customer_id)
                $data['customer_id'] = $customerIdFromUrl;

                $data['zona_nombre'] = $customer->zona?->nombre_zona;
                $data['region_nombre'] = $customer->regiones?->name;
                $data['zonas_id'] = $customer->zona?->id;
                $data['regiones_id'] = $customer->regiones?->id;

                $ultimoNumero = Pedido::where('customer_id', $customerIdFromUrl)->max('num_pedido');
                $nuevoNumero = $ultimoNumero ? ($ultimoNumero + 1) : 1;
                $data['num_pedido'] = $nuevoNumero;

                $data['fecha_entrega'] = Carbon::now()->addDays(15);
                $data['fecha_liquidacion'] = Carbon::now()->addDays(30);

                $data['day'] = Carbon::now()->day;
                $data['month'] = Carbon::now()->month;
                $data['year'] = Carbon::now()->year;

                $data['registrado_por'] = Auth::id();
            }


            $this->form->fill($data);


            return;
        }

        // Si no hay customer_id en la URL, llamamos al método base de Filament
        parent::fillForm();
    }
}
