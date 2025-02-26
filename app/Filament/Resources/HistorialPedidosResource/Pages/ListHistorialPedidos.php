<?php

namespace App\Filament\Resources\HistorialPedidosResource\Pages;

use App\Filament\Resources\HistorialPedidosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistorialPedidos extends ListRecords
{
    protected static string $resource = HistorialPedidosResource::class;
    protected static ?string $title = 'Historial de Pedidos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Pedido'),
        ];
    }
}
