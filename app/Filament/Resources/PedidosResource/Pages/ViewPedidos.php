<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPedidos extends ViewRecord
{
    protected static string $resource = PedidosResource::class;

     protected static ?string $title = 'Detalles del Pedido';
}
