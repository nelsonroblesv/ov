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
            Stat::make('Ordenes Pendientes', Order::query()->where('status', 'pending')->count()),
            Stat::make('Ordenes en Proceso', Order::query()->where('status', 'processing')->count()),
            Stat::make('Ordenes Completadas', Order::query()->where('status', 'completed')->count()),
            Stat::make('Total', Number::currency(Order::query()->sum('grand_total'), 'MXN'))
            
        ];
    }
}
