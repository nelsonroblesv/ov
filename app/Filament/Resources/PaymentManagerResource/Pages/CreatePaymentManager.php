<?php

namespace App\Filament\Resources\PaymentManagerResource\Pages;

use App\Filament\Resources\PaymentManagerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

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
}
