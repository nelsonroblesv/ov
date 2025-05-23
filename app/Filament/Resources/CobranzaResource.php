<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CobranzaResource\Pages;
use App\Filament\Resources\CobranzaResource\RelationManagers;
use App\Filament\Resources\CobranzaResource\RelationManagers\PagosRelationManager;
use App\Filament\Resources\PagoRelationManagerResource\RelationManagers\CobranzaResourceRelationManager;
use App\Models\Cobranza;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class CobranzaResource extends Resource
{
    protected static ?string $model = Cobranza::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Cobranza';
    protected static ?string $breadcrumb = "Cobranza";
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles')->schema([
                    Select::make('customer_id')
                        ->label('Cliente')
                        ->options(Customer::query()
                            ->where('is_active', true)
                            ->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->disabledOn('edit')
                        ->preload()
                        ->required(),

                    Select::make('tipo_semana')
                        ->label('Tipo de semana:')
                        ->placeholder('Selecciona el tipo de semana')
                        ->options([
                            0 => 'PAR',
                            1 => 'NON'
                        ]),

                    DateTimePicker::make('created_at')
                        ->label('Fecha de registro')
                        ->seconds(false)
                        ->required()
                        ->default(now()),

                    TextInput::make('saldo_total')
                        ->label('Saldo Total')
                        ->numeric()
                        ->required()
                        ->prefix('$'),

                    Hidden::make('codigo')
                        ->default(fn(Get $get) => 'COV-' . strtoupper(Str::random(5)) . '-' . $get('customer_id')),

                    Hidden::make('created_by')
                        ->default(auth()->id()),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'DESC')
            ->heading('Lista de Cobranzas')
            ->description('Esta es la lista de Saldos por cobrar. Usa los controles para ordenar por 
                            FECHA, TIPO DE SEMANA, SALDOS, o bien, usa la herramienta de filtro para obtener
                            informacion mas detallada. En esta tabla se muestra el Saldo Total (deuda)
                            así como también, el SALDO PENDIENTE.  Haz click o toca sobre cada registro 
                            para consultar el historial o realizar los Pagos correspondientes.')
            ->columns([
                // TextColumn::make('codigo')->label('Folio')->searchable(),
               // TextColumn::make('periodo')->label('Periodo')->sortable()->alignCenter(),
               // TextColumn::make('semana')->label('Semana')->sortable()->alignCenter(),
                TextColumn::make('tipo_semana')->label('Tipo')->sortable()->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        0 => 'PAR',
                        1 => 'NON'
                    ][$state] ?? 'Otro')
                    ->colors([
                        'success' => 0,
                        'info' => 1,
                    ])->alignCenter(),
                TextColumn::make('customer.name')->label('Cliente')->searchable(),
                TextColumn::make('saldo_total')
                    ->label('Saldo total')
                    ->money('MXN'),

                TextColumn::make('saldo_pendiente')
                    ->label('Saldo pendiente')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('is_pagada')->badge()
                    ->label('Estado')
                    ->formatStateUsing(fn(string $state): string => [
                        0 => 'Pendiente',
                        1 => 'Pagado',
                    ][$state] ?? 'Otro')

                    ->colors([
                        'success' => 1,
                        'warning' => 0,
                    ])
                    ->sortable(),
                TextColumn::make('created_at')->date()->label('Registrado'),
            ])
            ->filters([
                SelectFilter::make('tipo_semana')
                    ->label('Tipo Semana')
                    ->options([
                        0 => 'PAR',
                        1 => 'NON',
                       ])
                    ->placeholder('Todos'),

                    SelectFilter::make('is_pagada')
                    ->label('Estatus')
                    ->options([
                        0 => 'Pendiente',
                        1 => 'Pagado',
                       ])
                    ->placeholder('Todos'),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PagosRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCobranzas::route('/'),
            'create' => Pages\CreateCobranza::route('/create'),
            'edit' => Pages\EditCobranza::route('/{record}/edit'),
        ];
    }
}
