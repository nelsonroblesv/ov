<?php

namespace App\Filament\Resources\AdministrarVisitasResource\Widgets;

use App\Models\EntregaCobranzaDetalle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsVisitas extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('PROSPECTOS', EntregaCobranzaDetalle::query()
                ->where('tipo_visita', 'PR')
                ->where('status', 0)
                ->count())
                ->description('No visitados')
                ->icon('heroicon-o-cursor-arrow-ripple'),

            Stat::make('POSIBLES', EntregaCobranzaDetalle::query()
                ->where('tipo_visita', 'PO')
                ->where('status', 0)
                ->count())
                ->description('No visitados')
                 ->icon('heroicon-o-users'),

            Stat::make('PRIMER PEDIDO', EntregaCobranzaDetalle::query()
                ->where('tipo_visita', 'EP')
                ->where('status', 0)
                ->count())
                ->description('No visitados')
               ->icon('heroicon-o-numbered-list'),

            Stat::make('RECURRENTE', EntregaCobranzaDetalle::query()
                ->where('tipo_visita', 'ER')
                ->where('status', 0)
                ->count())
                ->description('No visitados')
                ->icon('heroicon-o-truck'),

            Stat::make('COBRANZA', EntregaCobranzaDetalle::query()
                ->where('tipo_visita', 'CO')
                ->where('status', 0)
                ->count())
                ->description('No visitados')
                ->icon('heroicon-o-banknotes'),


           // Stat::make('Total', Number::currency(Order::query()->sum('grand_total'), 'MXN'))
            
        ];
    }
}
