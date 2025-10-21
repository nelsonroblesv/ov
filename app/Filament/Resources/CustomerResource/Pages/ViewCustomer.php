<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerResource\Widgets\StatsCustomer as WidgetsStatsCustomer;
use Filament\Actions;
use Filament\Actions\Action as ActionsAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;
    protected static ?string $title = 'Vista de Cliente';

    protected function getHeaderActions(): array
    {
        return [
            ActionsAction::make('nuevoPedido')
                ->label('Nuevo Pedido')
                ->icon('heroicon-m-shopping-bag')
                ->color('success')
                ->action(function () {}),

            Actions\EditAction::make()
                ->label('Editar Información')
                ->icon('heroicon-m-pencil')
                ->color('warning'),

            Actions\DeleteAction::make()
                ->label('Borrar Cliente')
                ->icon('heroicon-o-user-minus')
                ->color('danger'),

        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
           WidgetsStatsCustomer::class
        ];
    }  
}
