<?php

namespace App\Filament\Resources\PaymentManagerResource\Pages;

use App\Filament\Resources\PaymentManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentManagers extends ListRecords
{
    protected static string $resource = PaymentManagerResource::class;
    protected static ?string $title = 'Administrar Pagos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Pago')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
