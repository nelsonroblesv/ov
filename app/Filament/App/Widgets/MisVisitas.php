<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\CustomerStatementResource;
use App\Filament\Resources\CustomerResource;
use Filament\Tables;
use App\Models\EntregaCobranzaDetalle;
use App\Models\Pedido;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
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
    protected static ?int $sort = 0;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Hoy ' . Carbon::now()->isoFormat('dddd D [de] MMMM, YYYY'))
            // ->description('Lista de visitas pendientes desde el '.Carbon::now()->startOfWeek()->isoFormat('dddd D [de] MMMM, YYYY') . ' hasta el ' . Carbon::now()->endOfWeek()->isoFormat('dddd D [de] MMMM, YYYY'))
            ->description('Visitas programadas.')
            ->emptyStateHeading('No hay visitas programadas para hoy')
            ->defaultSort('num_ruta', 'ASC')
            ->query(
                Pedido::query()
                    ->where('distribuidor', Auth::id())
                    ->where('estado_general', 'abierto')
                    ->whereDate('fecha_entrega', '=', Carbon::now())
            )
            ->columns([
                TextColumn::make('num_ruta')
                    ->label('# Ruta')
                    ->alignCenter(),

                TextColumn::make('num_ruta')
                    ->label('# Ruta')
                    ->alignCenter(),

                TextColumn::make('customer.name')
                    ->label('Detalles')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $customer = $record->customer?->name ?? 'N/E';
                        $customer_latitude = $record->customer?->latitude ?? '';
                        $customer_longitude = $record->customer?->longitude ?? '';
                        $region = $record->customer?->regiones?->name ?? 'Sin región';
                        $zona = $record->customer?->zona?->nombre_zona ?? 'Sin zona';
                        $phone = $record->customer?->phone ?? 'Sin zona';
                        return "
                                <span>👤 {$customer}</span><br>
                                <span>📍 {$region}</span><br>
                                <span><a href='https://www.google.com/maps/search/?api=1&query={$customer_latitude},{$customer_longitude}' target='_blank'>🗺️ {$zona}</a></span><br>
                                <span><a href='https://wa.me/" . urlencode($phone) . "'>📞 {$phone}</a></span><br>
                                ";
                    })

            ])
            ->filters([])
            ->actions([]);
    }
}
