<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\CustomerStatementResource;
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
    public function table(Table $table): Table
    {
         return $table
            ->heading('Visitas para hoy: ' . Carbon::now()->isoFormat('dddd, D [de] MMMM, YYYY'))
            ->query(
                EntregaCobranzaDetalle::query()
                    ->where('user_id', Auth::id())
                    ->with('customer', 'entregaCobranza')
            )
            ->columns([
                TextColumn::make('fecha_programada')
                    ->label('Fecha')
                    ->sortable()
                    ->date(),

                TextColumn::make('customer.regiones.name')
                    ->label('Region')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('customer.zona.nombre_zona')
                    ->label('Zona')
                    ->sortable()
                    ->badge()
                    ->color('danger'),

                TextColumn::make('customer.name')->label('Cliente'),
                TextColumn::make('tipo_visita')
                    ->label('Tipo')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'PR' => 'Prospecto',
                        'PO' => 'Posible',
                        'EP' => 'Entrega Primer Pedido',
                        'ER' => 'Entrega Recurrente',
                        'CO' => 'Cobranza',
                    ][$state] ?? 'Otro')
                    ->colors([
                        'danger' => 'PR',
                        'warning' => 'PO',
                        'info' => 'EP',
                        'success' => 'ER',
                        'primary' => 'CO',
                    ]),

                IconColumn::make('status')
                    ->label('Estatus')
                    ->sortable()
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('fecha_visita')->label('Visita')->date()
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->alignCenter(),

               IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->sortable()
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('fecha_visita')->label('Visita')->date()
                    ->alignCenter(),
            ])
            ->filters([
                Filter::make('hoy')
                    ->label('Hoy')
                    ->query(function (Builder $query) {
                        $query->whereHas('entregaCobranza', function ($q) {
                            $q->whereDate('fecha_programada', Carbon::now());
                        });
                    }),

                Filter::make('esta_semana')
                    ->label('Esta semana')
                    ->query(function (Builder $query) {
                        $query->whereHas('entregaCobranza', function ($q) {
                            $q->whereBetween('fecha_programada', [
                                now()->startOfWeek(),
                                now()->endOfWeek()
                            ]);
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_invoice')
                    ->label('Estado de Cuenta')
                    ->icon('heroicon-o-document-chart-bar')
                    ->url(fn($record) => CustomerStatementResource::getUrl(name: 'invoice', parameters: ['record' => $record->customer]))

                    ->openUrlInNewTab()
            ]);
    }
}
