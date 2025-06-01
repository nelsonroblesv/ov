<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\CustomerStatementResource;
use Filament\Pages\Page;
use Filament\Tables;
use App\Models\EntregaCobranzaDetalle;
use Carbon\Carbon;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;

class MisEntregas extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.app.pages.mis-entregas';
    protected static ?string $title = 'Itinerario de Visitas';
    protected static ?string $slug = 'itinerario-visitas';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Itinerario de Visitas';
    protected static ?string $breadcrumb = "Itinerario de Visitas";
    protected static ?int $navigationSort = 0;


    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                EntregaCobranzaDetalle::query()
                    ->where('user_id', Auth::id())
                    ->with('customer', 'entregaCobranza')
            )
            ->heading('Administrador de visitas programadas durante la semana.')
            ->description('Lista de visitas a realizar.')
            ->emptyStateHeading('No hay visitas programadas')
            ->defaultSort('fecha_programada', 'ASC')
            ->columns([
                TextColumn::make('fecha_programada')
                    ->label('Fecha')
                    ->sortable()
                    ->date(),

                TextColumn::make('customer_id')
                    ->label('Ubicaciones')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $region = $record->customer?->regiones?->name ?? 'Sin región';
                        $zona = $record->customer?->zona?->nombre_zona ?? 'Sin zona';

                        return "<span>📍 {$region}</span><br>
                                <span>📌 {$zona}</span>";
                    }),

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
                        'black' => 'CO',
                    ]),

                ToggleColumn::make('status')
                    ->label('Completado')
                    ->sortable()
                    ->alignCenter(),
                /*
                TextColumn::make('fecha_visita')->label('Visita')->date()
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->alignCenter(),
*/
                IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->sortable()
                    ->boolean()
                    ->alignCenter()
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
                ActionGroup::make([
                    Tables\Actions\Action::make('view_invoice')
                        ->label('Estado de Cuenta')
                        ->icon('heroicon-o-document-chart-bar')
                        ->url(fn($record) => CustomerStatementResource::getUrl(name: 'invoice', parameters: ['record' => $record->customer]))
                        ->openUrlInNewTab()
                ])
            ]);
    }
}
