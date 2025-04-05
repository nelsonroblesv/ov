<?php

namespace App\Filament\App\Resources\MisRutasResource\Pages;

use App\Filament\App\Resources\MisRutasResource;
use App\Models\GestionRutas;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListMisRutas extends ListRecords
{
    protected static string $resource = MisRutasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Lun' => Tab::make()
            ->label('Lunes')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('dia_semana', 'Lun')),

            'Mar' => Tab::make()
            ->label('Martes')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('dia_semana', 'Mar')),

            'Mie' => Tab::make()
            ->label('Miercoles')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('dia_semana', 'Mie')),

            'Jue' => Tab::make()
            ->label('Jueves')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('dia_semana', 'Jue')),

            'Vie' => Tab::make()
            ->label('Viernes')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('dia_semana', 'Vie')),
        ];
    }
    
}
