<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use App\Filament\Resources\PedidosResource\Widgets\StatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidosResource::class;
     protected static ?string $title = 'Historial de Pedidos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Registrar Pedido')
            ->icon('heroicon-o-shopping-bag'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return[
            StatsOverview::class
        ];
    }
}
