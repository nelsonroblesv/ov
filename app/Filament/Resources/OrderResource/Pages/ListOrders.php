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
            ->icon('heroicon-m-table-cells'),

            'pending' => Tab::make()
            ->icon('heroicon-m-clock')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'processing' => Tab::make()
            ->icon('heroicon-m-arrow-path')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing')),

            'completed' => Tab::make()
            ->icon('heroicon-m-check')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),

            'declined' => Tab::make()
            ->icon('heroicon-m-x-mark')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'declined'))
        ];
    }
}
