<?php

namespace App\Filament\App\Resources\PaymentManagerResource\Pages;

use App\Filament\App\Resources\PaymentManagerResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePaymentManager extends CreateRecord
{
    protected static string $resource = PaymentManagerResource::class;

    protected static ?string $title = 'Registrar Pago';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pago registrado')
            ->body('Se ha registrado un nuevo Pago de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function afterCreate(): void
    {
        $data = $this->record;
        $admin = User::where('role', 'Administrador')->get();
        $username = Auth::user()?->name ?? 'Usuario';
        $customer = $data->customer->name ?? 'Cliente desconocido';

        $body = "{$username} ha registrado una Cobranza a {$customer} por $". number_format($data->monto, 2);

        Notification::make()
            ->title('Cobranza registrada')
            ->body($body)
            ->icon('heroicon-o-banknotes')
            ->iconColor('info')
            ->color('info')
            ->sendToDatabase($admin);
    }
}
