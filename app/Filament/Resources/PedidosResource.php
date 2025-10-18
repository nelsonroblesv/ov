<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidosResource\Pages;
use App\Models\Customer;
use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidosResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $title = 'Pedidos';
    protected static ?string $slug = 'pedidos';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Ingresos';
    protected static ?string $navigationLabel = 'Gestionar Pedidos';
    protected static ?string $breadcrumb = "Gestionar Pedidos";
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {

        function calcularNumeroRuta(callable $set, callable $get): void
        {
            $dia = $get('dia_nota');
            $tipoSemana = $get('tipo_semana_nota');
            $periodo = $get('periodo');
            $semana = $get('semana');

            // Solo ejecuta si todos los campos están llenos
            if ($dia && $tipoSemana && $periodo && $semana) {
                $max = Pedido::where('dia_nota', $dia)
                    ->where('tipo_semana_nota', $tipoSemana)
                    ->where('periodo', $periodo)
                    ->where('semana', $semana)
                    ->max('num_ruta');

                $siguiente = $max ? $max + 1 : 1;

                $set('num_ruta', $siguiente);
            } else {
                $set('num_ruta', null);
            }
        }
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
                                    ->dehydrated()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $customer = Customer::with(['zona', 'regiones'])->find($state);
                                        if ($customer) {
                                            $set('zona_nombre', $customer->zona?->nombre_zona);
                                            $set('region_nombre', $customer->regiones?->name);
                                            $set('zonas_id', $customer->zona?->id);
                                            $set('regiones_id', $customer->regiones?->id);

                                            $ultimoNumero = Pedido::where('customer_id', $state)
                                                ->max('num_pedido');
                                            $nuevoNumero = $ultimoNumero ? ($ultimoNumero + 1) : 1;
                                            $set('num_pedido', $nuevoNumero);
                                        } else {
                                            $set('zona_nombre', null);
                                            $set('region_nombre', null);
                                            $set('zonas_id', null);
                                            $set('regiones_id', null);
                                            $set('num_pedido', null);
                                        }
                                    })
                                    ->afterStateHydrated(function ($state, callable $set, $get) {
                                        $customer = Customer::with(['zona', 'regiones'])->find($state);
                                        if ($customer) {
                                            $set('zona_nombre', $customer->zona?->nombre_zona);
                                            $set('region_nombre', $customer->regiones?->name);
                                            $set('zonas_id', $customer->zona?->id);
                                            $set('regiones_id', $customer->regiones?->id);

                                            if (!$get('num_pedido')) {
                                                $ultimoNumero = Pedido::where('customer_id', $state)->max('num_pedido');
                                                $set('num_pedido', $ultimoNumero ? ($ultimoNumero + 1) : 1);
                                            }
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
                                    ->disabled()
                                    ->dehydrated(),

                                Select::make('tipo_nota')
                                    ->label('Tipo de Nota')
                                    ->suffixIcon('heroicon-m-document-text')
                                    ->options([
                                        'sistema' => 'SISTEMA',
                                        'real' => 'REAL',
                                        'stock' => 'DESDE STOCK'
                                    ])
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state === 'stock') {
                                            do {
                                                $folio = 'OV-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
                                            } while (Pedido::where('id_nota', $folio)->exists());

                                            $set('id_nota', $folio);
                                        } else {
                                            $set('id_nota', null);
                                        }
                                    }),

                                TextInput::make('id_nota')
                                    ->label('ID de Nota')
                                    ->disabled(fn(callable $get) => $get('tipo_nota') === 'stock')
                                    ->required()
                                    ->unique(ignoreRecord: true, column: 'id_nota')
                                    ->dehydrated(),

                                Select::make('tipo_semana_nota')
                                    ->label('Tipo de Semana')
                                    ->suffixIcon('heroicon-m-calendar')
                                    ->options([
                                        'P' => 'PAR',
                                        'N' => 'NON'
                                    ])
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) => calcularNumeroRuta($set, $get)),

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
                                    ])
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) => calcularNumeroRuta($set, $get)),

                                Select::make('semana')
                                    ->label('Semana')
                                    ->suffixIcon('heroicon-m-calendar-days')
                                    ->options([
                                        '1' => 'S1',
                                        '2' => 'S2',
                                        '3' => 'S3',
                                        '4' => 'S4'
                                    ])
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) => calcularNumeroRuta($set, $get)),

                                Select::make('dia_nota')
                                    ->label('Día de Nota')
                                    ->suffixIcon('heroicon-m-calendar-days')
                                    ->options([
                                        'L' => 'LUNES',
                                        'M' => 'MARTES',
                                        'X' => 'MIÉRCOLES',
                                        'J' => 'JUEVES',
                                        'V' => 'VIERNES'
                                    ])
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) => calcularNumeroRuta($set, $get)),

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
                                    ])
                                    ->required()
                                    ->reactive()
                                     ->afterStateUpdated(fn (callable $set) => $set('real_id', null)),

                                Select::make('real_id')
                                    ->label(fn(callable $get) => match ($get('estado_pedido')) {
                                        'cambio' => 'Selecciona un cliente real',
                                        'susana' => 'Selecciona un usuario',
                                        default => 'Selecciona una opción',
                                    })
                                    ->options(function (callable $get) {
                                        return match ($get('estado_pedido')) {
                                            'cambio' => Customer::pluck('name', 'id'),
                                            'susana' => User::pluck('name', 'id'),
                                            default => [],
                                        };
                                    })
                                    ->searchable()
                                    ->reactive()
                                    ->required()
                                    ->visible(fn(callable $get) => in_array($get('estado_pedido'), ['cambio', 'susana']))
                                    ->columnSpanFull(),
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
                                    ->suffixIcon('heroicon-m-map')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Completa el Dia de Nota, Tipo Semana, Periodo y Semana.',
                                    ]),

                                DatePicker::make('fecha_entrega')
                                    ->label('Fecha de Entrega')
                                    ->suffixIcon('heroicon-m-calendar-date-range')
                                    ->default(Carbon::now()->addDays(15)),

                                DatePicker::make('fecha_liquidacion')
                                    ->label('Fecha de Liquidación')
                                    ->suffixIcon('heroicon-m-calendar-date-range')
                                    ->default(Carbon::now()->addDays(30)),

                                Select::make('distribuidor')
                                    ->label('Distribuidor')
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

                                Hidden::make('day')->default(fn() => Carbon::now()->day),
                                Hidden::make('month')->default(fn() => Carbon::now()->month),
                                Hidden::make('year')->default(fn() => Carbon::now()->year),

                                Hidden::make('registrado_por')->default(fn() => Auth::id()),

                            ])->columns(2)
                        ]),
                ])->columnSpanFull()
                    ->startOnStep(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([])
            ->filters([])
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
