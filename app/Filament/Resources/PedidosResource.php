<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidosResource\Pages;
use App\Models\Customer;
use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PedidosResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $title = 'Pedidos';
    protected static ?string $slug = 'pedidos';
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Gestionar Pedidos';
    protected static ?string $breadcrumb = "Gestionar Pedidos";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Cliente')
                        ->schema([
                            Section::make()->schema([
                                Select::make('customer_id')
                                    ->required()
                                    ->label('Cliente')
                                    ->searchable()
                                    ->suffixIcon('heroicon-m-user')
                                    ->columnSpanFull()
                                    ->disabledOn('edit')
                                    ->options(Customer::query()
                                        ->where('is_active', true)
                                        ->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
                                        ->orderBy('name', 'ASC')
                                        ->pluck('name', 'id'))
                                    ->reactive()
                                    ->preload()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $customer = Customer::with(['zona', 'regiones'])->find($state);
                                        if ($customer) {
                                            $set('zona_nombre', $customer->zona?->nombre_zona);
                                            $set('region_nombre', $customer->regiones?->name);
                                            $set('zonas_id', $customer->zona?->id);
                                            $set('regiones_id', $customer->regiones?->id);
                                        } else {
                                            $set('zona_nombre', null);
                                            $set('region_nombre', null);
                                            $set('zonas_id', null);
                                            $set('regiones_id', null);
                                        }
                                    })
                                    ->afterStateHydrated(function ($state, callable $set) {
                                        $customer = Customer::with(['zona', 'regiones'])->find($state);
                                        if ($customer) {
                                            $set('zona_nombre', $customer->zona?->nombre_zona);
                                            $set('region_nombre', $customer->regiones?->name);
                                            $set('zonas_id', $customer->zona?->id);
                                            $set('regiones_id', $customer->regiones?->id);
                                        }
                                    }),

                                TextInput::make('zona_nombre')
                                    ->label('Zona')
                                    ->suffixIcon('heroicon-m-map')
                                    ->disabled()
                                    ->dehydrated(false),

                                Hidden::make('zonas_id')
                                    ->dehydrated(true),
                                Hidden::make('regiones_id')
                                    ->dehydrated(true),

                                TextInput::make('region_nombre')
                                    ->label('Región')
                                    ->suffixIcon('heroicon-m-map-pin')
                                    ->disabled()
                                    ->dehydrated(false),


                                Select::make('customer_type')
                                    ->label('Tipo de Cliente')
                                    ->suffixIcon('heroicon-m-cursor-arrow-rays')
                                    ->options([
                                        'N' => 'NUEVO',
                                        'R' => 'RECURRENTE'
                                    ])
                                    ->default('R'),

                                Select::make('factura')
                                    ->label('Factura')
                                    ->options([
                                        '1' => 'SI',
                                        '0' => 'NO'
                                    ])
                                    ->default('0')
                                    ->suffixIcon('heroicon-m-document-currency-dollar'),

                            ])->columns(2)

                        ]),
                    Step::make('Pedido')
                        ->schema([
                            Section::make()->schema([
                                TextInput::make('num_pedido')
                                    ->label('# Pedido')
                                    ->suffixIcon('heroicon-m-hashtag')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                DatePicker::make('fecha_pedido')
                                    ->label('Fecha del Pedido')
                                    ->suffixIcon('heroicon-m-calendar')
                                    ->default(Carbon::now()),

                                Select::make('tipo_nota')
                                    ->label('Tipo de Nota')
                                    ->suffixIcon('heroicon-m-document-text')
                                    ->options([
                                        'sistema' => 'SISTEMA',
                                        'real' => 'REAL',
                                        'stock' => 'DESDE STOCK'
                                    ]),

                                Select::make('tipo_semana_nota')
                                    ->label('Tipo de Semana')
                                    ->suffixIcon('heroicon-m-calendar')
                                    ->options([
                                        'P' => 'PAR',
                                        'N' => 'NON'
                                    ]),

                                Select::make('periodo')
                                    ->label('Periodo')
                                    ->suffixIcon('heroicon-m-calendar-date-range')
                                    ->options([
                                        '1' => 'P01',
                                        '2' => 'P02',
                                        '3' => 'P03',
                                        '4' => 'P04',
                                        '5' => 'P05',
                                        '6' => 'P06',
                                        '7' => 'P07',
                                        '8' => 'P08',
                                        '9' => 'P09',
                                        '10' => 'P10',
                                        '11' => 'P11',
                                        '12' => 'P12',
                                        '13' => 'P13'
                                    ]),

                                Select::make('semana')
                                    ->label('Semana')
                                    ->suffixIcon('heroicon-m-calendar-days')
                                    ->options([
                                        '1' => 'S1',
                                        '2' => 'S2',
                                        '3' => 'S3',
                                        '4' => 'S4'
                                    ]),

                                Select::make('dia_nota')
                                    ->label('Día de Nota')
                                    ->suffixIcon('heroicon-m-calendar-days')
                                    ->options([
                                        'L' => 'LUNES',
                                        'M' => 'MARTES',
                                        'X' => 'MIÉRCOLES',
                                        'J' => 'JUEVES',
                                        'V' => 'VIERNES'
                                    ]),

                                Select::make('estado_pedido')
                                    ->label('Estado del Pedido')
                                    ->suffixIcon('heroicon-m-check-circle')
                                    ->options([
                                        'cambio' => 'CAMBIO',
                                        'cancelado' => 'CANCELADO',
                                        'entrega' => 'ENTREGA',
                                        'pagado' => 'PAGADO',
                                        'pendiente' => 'PENDIENTE',
                                        'reposicion' => 'REPOSICIÓN',
                                        'susana' => 'SUSANA'
                                    ]),

                            ])->columns(2)

                        ]),
                    Step::make('Pago y Entrega')
                        ->schema([
                            Section::make()->schema([
                                TextInput::make('monto')
                                    ->label('Monto del Pedido')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('0.00')
                                    ->suffixIcon('heroicon-m-currency-dollar'),

                                TextInput::make('num_ruta')
                                    ->label('# Ruta')
                                    ->suffixIcon('heroicon-m-map'),

                                DatePicker::make('fecha_entrega')
                                    ->label('Fecha de Entrega')
                                    ->suffixIcon('heroicon-m-calendar-date-range')
                                    ->default(Carbon::now()->addDays(15)),

                                DatePicker::make('fecha_liquidacion')
                                    ->label('Fecha de Liquidación')
                                    ->suffixIcon('heroicon-m-calendar-date-range')
                                    ->default(Carbon::now()->addDays(15)),

                                Select::make('entrega')
                                    ->label('Entrega')
                                    ->suffixIcon('heroicon-m-archive-box-arrow-down')
                                    ->options(User::query()
                                        ->where('is_active', true)
                                        ->whereIn('role', ['Vendedor'])
                                        ->orderBy('name', 'ASC')
                                        ->pluck('name', 'id')),

                                Select::make('reparto')
                                    ->label('Reparto')
                                    ->suffixIcon('heroicon-m-truck')
                                    ->options(User::query()
                                        ->where('is_active', true)
                                        ->whereIn('role', ['Vendedor', 'Repartidor'])
                                        ->orderBy('name', 'ASC')
                                        ->pluck('name', 'id')),

                                Textarea::make('observaciones')
                                    ->label('Observaciones')
                                    ->columnSpanFull(),

                                FileUpload::make('notas_venta')
                                    ->label('Notas de Venta')
                                    ->placeholder('Haz click para cargar la(s) nota(s) de venta')
                                    ->multiple()
                                    ->directory('notas_venta')
                                    ->openable()
                                    ->downloadable()
                                    ->columnSpanFull(),

                                Hidden::make('registrado_por')->default(fn() => auth()->id()),

                            ])->columns(2)
                        ]),
                ])->columnSpanFull()
                    ->startOnStep(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('fecha_pedido')
                    ->label('Fecha de Pedido')
                    ->searchable()
                    ->sortable()
                    ->date(),

                TextColumn::make('num_pedido')
                    ->label('# Pedido')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipo_semana_nota')
                    ->label('PAR/NON')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'P' => 'PAR',
                        'N' => 'NON'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color(fn(string $state): string => [
                        'P' => 'success',
                        'N' => 'info'
                    ][$state] ?? 'primary'),

                TextColumn::make('dia_nota')
                    ->label('Día Nota')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'L' => 'LUNES',
                        'M' => 'MARTES',
                        'X' => 'MIÉRCOLES',
                        'J' => 'JUEVES',
                        'V' => 'VIERNES'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('userDistribuidor.name')
                    ->label('Distribuidor')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-building-library'),

                TextColumn::make('userEntrega.name')
                    ->label('Entrega')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->icon('heroicon-m-archive-box-arrow-down'),

                TextColumn::make('userReparto.name')
                    ->label('Reparto')
                    ->searchable()
                    ->sortable()->badge()
                    ->color('info')
                    ->icon('heroicon-m-archive-box-arrow-down'),

                TextColumn::make('customer_type')
                    ->label('Tipo Cliente')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'N' => 'NUEVO',
                        'R' => 'RECURRENTE'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color(fn(string $state): string => [
                        'N' => 'success',
                        'R' => 'info'
                    ][$state] ?? 'primary'),

                TextColumn::make('zona.nombre_zona')
                    ->label('Zona')
                    ->searchable()
                    ->sortable()
                     ->color('info'),

                TextColumn::make('region.name')
                    ->label('Región')
                    ->searchable()
                    ->sortable()
                    ->color('primary'),

                IconColumn::make('factura')
                    ->label('Factura')
                    ->searchable()
                    ->boolean()
                    ->sortable(),

                TextColumn::make('periodo')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('semana')
                    ->badge()
                    ->color('danger')
                    ->alignCenter(),

                TextColumn::make('num_ruta')
                    ->alignCenter(),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fecha_entrega')
                    ->label('Fecha Entrega')
                    ->searchable()
                    ->sortable()
                    ->date(),

                TextColumn::make('tipo_nota')
                    ->label('Tipo Nota')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'sistema' => 'SISTEMA',
                        'real' => 'REAL',
                        'stock' => 'DE STOCK'
                    ][$state] ?? 'Otro')
                     ->formatStateUsing(fn(string $state): string => [
                        'sistema' => 'SISTEMA',
                        'real' => 'REAL',
                        'stock' => 'STOCK'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color(fn(string $state): string => [
                        'sistema' => 'success',
                        'real' => 'info',
                        'stock' => 'warning'
                    ][$state] ?? 'primary'),

                TextColumn::make('monto')
                    ->badge()
                    ->color('info')
                     ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2)),

                TextColumn::make('estado_pedido')
                    ->label('Tipo Nota')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'cambio' => 'CAMBIO',
                        'cancelado' => 'CANCELADO',
                        'entrega' => 'ENTREGA',
                        'pagado' => 'PAGADO',
                        'pendiente' => 'PENDIENTE',
                        'reposicion' => 'REPOSICIÓN',
                        'susana' => 'SUSANA'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color(fn(string $state): string => [
                        'cambio' => 'info',
                        'cancelado' => 'danger',
                        'entrega' => 'warning',
                        'pagado' => 'success',
                        'pendiente' => 'primary',
                        'reposicion' => 'info',
                        'susana' => 'light'
                    ][$state] ?? 'primary'),

                TextColumn::make('fecha_liquidacion')
                    ->label('Fecha Liquidación')
                    ->searchable()
                    ->sortable()
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('tipo_semana_nota')
                    ->label('Tipo Semana')
                    ->options([
                        'P' => 'PAR',
                        'N' => 'NON'
                    ]),

                SelectFilter::make('dia_nota')
                    ->label('Día Nota')
                    ->options([
                        'L' => 'LUNES',
                        'M' => 'MARTES'
                    ])
                    ->multiple(),

                SelectFilter::make('customer_type')
                    ->label('Tipo Cliente')
                    ->options([
                        'N' => 'NUEVO',
                        'R' => 'RECURRENTE'
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedidos::route('/create'),
            'edit' => Pages\EditPedidos::route('/{record}/edit'),
        ];
    }
}
