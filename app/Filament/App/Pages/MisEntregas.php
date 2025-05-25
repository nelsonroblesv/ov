<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use App\Models\EntregaCobranzaDetalle;
use DeepCopy\TypeFilter\Date\DatePeriodFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

class MisEntregas extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.app.pages.mis-entregas';
     protected static ?string $title = 'Mis Entregas y Cobranzas';
    protected static ?string $slug = 'mis-entregas-cobranzas';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Mis Entregas y Cobranzas';
    protected static ?string $breadcrumb = "Mis Entregas y Cobranzas";
     protected static ?int $navigationSort = 0;
    

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                EntregaCobranzaDetalle::query()
                    ->where('user_id', Auth::id())
            )
            ->columns([
                TextColumn::make('customer.regiones.name')
                    ->label('Region')
                    ->sortable(),

                TextColumn::make('customer.zona.nombre_zona')
                    ->label('Zona')
                    ->sortable(),
                TextColumn::make('customer.name')->label('Cliente'),
                TextColumn::make('tipo')->label('Tipo')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'E' => 'Entrega',
                        'C' => 'Cobranza',

                    ][$state] ?? 'Otro')
                    ->colors([
                        'success' => 'E',
                        'warning' => 'C'
                    ]),

                TextColumn::make('entregaCobranza.fecha_programada')->label('Fecha Programada')->date(),
            ])
            ->filters([
               

               
            ]);
    }
}
