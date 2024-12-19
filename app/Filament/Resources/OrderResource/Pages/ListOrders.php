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

            'pending' => Tab::make()
            ->label('Pendientes')
            ->icon('heroicon-m-clock')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'processing' => Tab::make()
            ->label('Procesando')
            ->icon('heroicon-m-arrow-path')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing')),

            'completed' => Tab::make()
            ->label('Completos')
            ->icon('heroicon-m-check')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),

            'declined' => Tab::make()
            ->label('Rechazados')
            ->icon('heroicon-m-x-mark')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'declined'))
        ];
    }
}
