<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;

class ViewPedidos extends ViewRecord
{
    protected static string $resource = PedidosResource::class;

     protected static ?string $title = 'Detalles del Pedido';

     protected function getHeaderActions(): array
     {
            return [
                Actions\EditAction::make()
                    ->label('Editar Pedido')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),

                Actions\DeleteAction::make()
                     ->label('Borrar Pedido')
                     ->icon('heroicon-o-trash'),

                Action::make('nota')
                    ->label('Generar Nota')
                    ->icon('heroicon-o-document-text')
                    ->color('info'),
            ];
     }

}
