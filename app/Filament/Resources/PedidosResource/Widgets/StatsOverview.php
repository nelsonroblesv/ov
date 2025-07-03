<?php

namespace App\Filament\Resources\PedidosResource\Widgets;

use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Nuevos', Pedido::query()
                //->where('estado_pedido', 'cancelado')
                ->where('customer_type', 'N')
                ->count())
                ->description('Total: $' . number_format(Pedido::query()->where('customer_type', 'N')->sum('monto'), 2))
                ->descriptionIcon('heroicon-m-arrow-up-right')
                ->color('success')
                ->chart([7, 7, 7, 7, 7, 7, 7]),

            Stat::make('Recurrentes', Pedido::query()
                //->where('estado_pedido', 'pendiente')
                ->where('customer_type', 'R')
                ->count())
                ->description('Total: $' . number_format(Pedido::query()->where('customer_type', 'R')->sum('monto'), 2))
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color('info')
                ->chart([7, 7, 7, 7, 7, 7, 7]),

            Stat::make('Cancelados Nuevos', Pedido::query()
                ->where('estado_pedido', 'cancelado')
                ->where('customer_type', 'N')
                ->count())
                ->description('Total: $' . number_format(Pedido::query()->where('customer_type', 'N')->sum('monto'), 2))
                ->descriptionIcon('heroicon-m-x-mark')
                ->color('danger')
                ->chart([7, 7, 7, 7, 7, 7, 7]),

            Stat::make('Cancelados Recurrentes', Pedido::query()
                ->where('estado_pedido', 'cancelado')
                ->where('customer_type', 'R')
                ->count())
                ->description('Total: $' . number_format(Pedido::query()->where('estado_pedido', 'cancelado')->where('customer_type', 'R')->sum('monto'), 2))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([7, 7, 7, 7, 7, 7, 7]),

            Stat::make('Total General', '$'.number_format(Pedido::query()->sum('monto'), 2))
                ->description('Hoy: $' . number_format(Pedido::query()->where('created_at', Carbon::now())->sum('monto'), 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
/*
           Stat::make('Saldo Total', '$'.number_format(Pedido::query()->sum('monto'), 2))
                ->description('Hoy: $' . number_format(Pedido::query()->where('created_at', Carbon::now())->sum('monto'), 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
                */
        ];
    }
}
