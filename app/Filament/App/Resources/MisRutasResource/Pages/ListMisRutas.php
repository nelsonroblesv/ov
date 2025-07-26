<?php

namespace App\Filament\App\Resources\MisRutasResource\Pages;

use App\Filament\App\Resources\MisRutasResource;
use App\Models\GestionRutas;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListMisRutas extends ListRecords
{
    protected static string $resource = MisRutasResource::class;
    protected static ?string $title = 'Administrar Rutas';

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Lun' => Tab::make()
            ->label('Lunes')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('fecha_entrega', Carbon::now()->startOfWeek()->toDateString())),

            'Mar' => Tab::make()
            ->label('Martes')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('fecha_entrega', Carbon::now()->startOfWeek()->addDay(1)->toDateString())),

            'Mie' => Tab::make()
            ->label('Miercoles')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('fecha_entrega', Carbon::now()->startOfWeek()->addDay(2)->toDateString())),

            'Jue' => Tab::make()
            ->label('Jueves')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('fecha_entrega',Carbon::now()->startOfWeek()->addDay(3)->toDateString())),

            'Vie' => Tab::make()
            ->label('Viernes')
            ->icon('heroicon-o-truck')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('fecha_entrega', Carbon::now()->startOfWeek()->addDay(4)->toDateString())),
        ];
    }
    
}
