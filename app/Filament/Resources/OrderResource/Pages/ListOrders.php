<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Pedidos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Pedido'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return[
            OrderStats::class
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All')
            ->label('Todos')
            ->icon('heroicon-m-table-cells'),

            'PEN' => Tab::make()
            ->label('Pendientes')
            ->icon('heroicon-o-exclamation-circle')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'PEN')),

            'COM' => Tab::make()
            ->label('Completos')
            ->icon('heroicon-o-check')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'COM')),

            'REC' => Tab::make()
            ->label('Rechazados')
            ->icon('heroicon-o-x-mark')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REC')),

            'REU' => Tab::make()
            ->label('Reubicados')
            ->icon('heroicon-o-map-pin')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REU')),

            'DEV' => Tab::make()
            ->label('Dev Parcial')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DEV')),

            'SIG' => Tab::make()
            ->label('Sig Visita')
            ->icon( 'heroicon-o-calendar-date-range')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'SIG'))
        ];
    }
}
