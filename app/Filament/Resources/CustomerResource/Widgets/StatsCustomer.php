<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsCustomer extends BaseWidget
{

    protected ?string $heading = 'Analitica del Cliente';
    protected ?string $description = 'Vision general de la analitica del cliente.';

    protected function getStats(): array
    {
        return [
            Stat::make('Cuentas por cobrar', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Anticipos recibidos', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Anticipos entregados', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Pedidos por entregar', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Pedidos entregados', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),

            Stat::make('Total de venta', Customer::query()
                ->where('is_active', true)
                ->count())
                ->color('success'),
        ];
    }
}
