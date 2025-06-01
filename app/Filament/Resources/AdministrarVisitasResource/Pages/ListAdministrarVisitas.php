<?php

namespace App\Filament\Resources\AdministrarVisitasResource\Pages;

use App\Filament\Resources\AdministrarVisitasResource;
use App\Filament\Resources\AdministrarVisitasResource\Widgets\StatsVisitas;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListAdministrarVisitas extends ListRecords
{
    protected static string $resource = AdministrarVisitasResource::class;
     protected static ?string $title = 'Administrar Visitas';

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return[
            StatsVisitas::class
        ];
    }

    public function getTabs(): array
    {
        return [
            'ALL' => Tab::make('All')
            ->label('Todos')
            ->icon('heroicon-m-table-cells')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('id', '>', 0)),

            'PR' => Tab::make()
            ->label('Prospectos')
            ->icon('heroicon-o-cursor-arrow-ripple')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo_visita', 'PR')),

            'PO' => Tab::make()
            ->label('Posibles')
            ->icon('heroicon-o-users')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo_visita', 'PO')),

            'EP' => Tab::make()
            ->label('Primer Pedido')
            ->icon('heroicon-o-numbered-list')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo_visita', 'EP')),

            'ER' => Tab::make()
            ->label('Entrega Recurrente')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo_visita', 'ER')),

            'CO' => Tab::make()
            ->label('Cobranza')
            ->icon('heroicon-o-banknotes')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo_visita', 'CO')),
        ];
    }
}
