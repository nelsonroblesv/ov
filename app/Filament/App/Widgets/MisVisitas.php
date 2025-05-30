<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\CustomerStatementResource;
use App\Filament\Resources\CustomerResource;
use Filament\Tables;
use App\Models\EntregaCobranzaDetalle;
use Carbon\Carbon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

Carbon::setLocale('es');

class MisVisitas extends BaseWidget
{

    //protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Visitas para hoy: ' . Carbon::now()->isoFormat('dddd D [de] MMMM, YYYY'))
            ->query(
                EntregaCobranzaDetalle::query()
                    ->where('user_id', Auth::id())
                    ->whereDate('fecha_programada', '<=', Carbon::now())
                    ->where('status', false)
                    ->with('customer', 'entregaCobranza')
            )
            ->columns([


                TextColumn::make('customer.zona.nombre_zona')
                    ->label('Ubicación')
                    ->color('info')
                    ->description(fn($record) => $record->customer?->regiones?->name ?? 'Sin información'),

                TextColumn::make('tipo_visita')
                    ->label('Visita')
                    ->sortable()
                    ->html() // permite HTML (importante para <br>)
                    ->formatStateUsing(function ($record) {
                        $customer = $record->customer?->name ?? 'Sin información';
                        $phone = $record->customer?->phone ?? 'Sin información';
                        return "
                            <span class='text-blue-600 font-semibold'>{$customer}</span><br>
                            <span class='text-red-600'>{$phone}</span>";
                    })
                    //->description(fn($record) => $record->customer?->name ?? 'Sin información')
            ])
            ->filters([])
            ->actions([]);
    }
}
