<?php

namespace App\Filament\App\Resources\PaymentManagerResource\Pages;

use App\Filament\App\Resources\PaymentManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentManager extends ViewRecord
{
    protected static string $resource = PaymentManagerResource::class;
     protected static ?string $title = 'Detalles del Pago';
}
