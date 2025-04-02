<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('PENDIENTES', Order::query()->where('status', 'PEN')->count()),
            Stat::make('COMPLETOS', Order::query()->where('status', 'COM')->count()),
            Stat::make('REUBICADOS', Order::query()->where('status', 'REU')->count()),
            Stat::make('DEVUELTOS', Order::query()->where('status', 'DEV')->count()),
            Stat::make('SIGUIENTE VISITA', Order::query()->where('status', 'SIG')->count()),
           // Stat::make('Total', Number::currency(Order::query()->sum('grand_total'), 'MXN'))
            
        ];
    }
}
