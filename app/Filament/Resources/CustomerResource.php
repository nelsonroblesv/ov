<?php

namespace App\Filament\Resources;

use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use App\Enums\CfdiTypeEnum;
use App\Enums\SociedadTypeEnum;
use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\PaquetesInicio;
use App\Models\Regiones;
use App\Models\Zonas;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter as FiltersFilter;
use Filament\Tables\Table;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\Reactive;
use PhpParser\ErrorHandler\Collecting;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Clientes & Prospectos';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $breadcrumb = "Clientes";
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Basicos')
                        ->description('Informacion Personal')
                        ->schema([
                            Section::make('Datos personales')->schema([

                                Hidden::make('alta_user_id')->default(fn() => auth()->id()),

                                Select::make('user_id')
                                    ->required()
                                    ->relationship('user', 'name')
                                    ->label('Asignado a:'),

                                TextInput::make('name')
                                    ->label('Nombre completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->extraInputAttributes(['onInput' => 'this.value = this.value.toUpperCase()'])
                                    ->suffixIcon('heroicon-m-user'),

                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->required()
                                    ->rules([
                                        'regex:/^[a-zA-Z0-9._%+-ñÑ]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                                    ])
                                    ->unique(ignoreRecord:true)
                                    ->placeholder('ejemplo@dominio.com')
                                    ->suffixIcon('heroicon-m-at-symbol'),

                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->suffixIcon('heroicon-m-phone'),

                                Select::make('simbolo')
                                    ->label('Simbologia de')
                                    ->required()
                                    ->options([
                                        'SB' => 'Salón de Belleza',
                                        'SYB' => 'Salón y Barbería',
                                        'EU' => 'Estética Unisex',
                                        'BB' => 'Barbería',
                                        'UN' => 'Salón de Uñas',
                                        'OS' => 'OSBERTH',
                                        'CR' => 'Cliente Pedido Rechazado',
                                        'UB' => 'Ubicación en Grupo',
                                        'NC' => 'Ya no compran'
                                    ]),

                                DatePicker::make('birthday')
                                    ->label('Fecha de nacimiento')
                                    //->required()
                                    ->suffixIcon('heroicon-m-cake'),

                                FileUpload::make('avatar')
                                    ->label('Avatar')
                                    ->image()
                                    ->avatar()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->directory('customer-avatar')
                            ])->columns(2),

                            Section::make('Sistema')->schema([
                                ToggleButtons::make('tipo_cliente')
                                    ->label('Tipo de Cliente')
                                    ->inline()
                                    ->options([
                                        'PV' => 'Punto Venta',
                                        'RD' => 'Red',
                                        'BK' => 'Black',
                                        'SL' => 'Silver',
                                        'PO' => 'Posible',
                                        'PR' => 'Prospecto',
                                    ])
                                    ->default('PV')
                                    ->colors([
                                        'PV' => 'success',
                                        'RD' => 'danger',
                                        'BK' => 'info',
                                        'SL' => 'warning',
                                        'PO' => 'danger',
                                        'PR' => 'warning',
                                    ])
                                    ->icons([/*
                                        'PV' => 'heroicon-o-building-storefront',
                                        'RD' => 'heroicon-o-user',
                                        'BK' => 'heroicon-o-star',
                                        'SL' => 'heroicon-o-sparkles'*/]),
                                Select::make('paquete_inicio_id')
                                    ->label('Paquete de inicio')
                                    ->options(
                                        PaquetesInicio::all()->mapWithKeys(function ($paquete) {
                                            return [
                                                $paquete->id => "{$paquete->prefijo} {$paquete->nombre} ({$paquete->precio} MXN)"
                                            ];
                                        })
                                    )
                                    ->placeholder('Selecciona un paquete')
                                    ->required(),
                            ])
                        ])->columns(2),

                    Step::make('Negocio')
                        ->description('Informacion del establecimiento')
                        ->schema([
                            TextInput::make('full_address')
                                ->label('Dirección')
                                ->helperText('Calle, Núm. Ext., Núm. Int., Colonia, Intersecciones')
                                // ->required()
                                ->maxLength(255)
                                ->suffixIcon('heroicon-m-map')
                                ->columnSpanFull(),


                            Map::make('location')
                                ->mapControls([
                                    'mapTypeControl'    => false,
                                    'scaleControl'      => true,
                                    'streetViewControl' => false,
                                    'rotateControl'     => true,
                                    'fullscreenControl' => true,
                                    'searchBoxControl'  => false,
                                    'zoomControl'       => true,
                                ])
                                ->defaultZoom(15)
                                ->autocomplete('full_address')
                                ->autocompleteReverse(true)
                                ->reverseGeocode([
                                    'street' => '%n %S',
                                    'city' => '%L',
                                    'state' => '%A1',
                                    'zip' => '%z',
                                ])

                                ->layers([
                                    'https://app.osberthvalle.com/storage/maps/zonas_ovalle.kml',
                                ])

                                ->debug()
                                ->draggable()

                                ->geolocate()
                                ->geolocateLabel('Obtener mi Ubicacion')
                                ->geolocateOnLoad(true, false)

                                ->defaultLocation(fn($record) => [
                                    $record->latitude ?? 19.8386943,
                                    $record->longitude ?? -90.4982317,
                                ])
                                ->columnSpanFull()->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                }),

                            Hidden::make('latitude')
                                ->label('Latitud')
                                ->helperText('Formato: 20.1845751')
                                // ->unique(ignoreRecord: true)
                                ->reactive()
                                ->dehydrated()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('location', [
                                        'lat' => floatVal($state),
                                        'lng' => floatVal($get('longitude')),
                                    ]);
                                })->lazy(),

                            Hidden::make('longitude')
                                ->label('Longitud')
                                ->helperText('Formato: 20.1845751')
                                //->unique(ignoreRecord: true)
                                ->dehydrated()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('location', [
                                        'lat' => floatval($get('latitude')),
                                        'lng' => floatVal($state),
                                    ]);
                                })->lazy(),


                            Select::make('regiones_id')
                                ->label('Región')
                                //  ->required()
                                ->options(
                                    fn() =>
                                    Regiones::whereIn('id', function ($query) {
                                        $query->select('regiones_id')
                                            ->from('zonas');
                                    })->pluck('name', 'id')
                                )
                                ->reactive(),

                            Select::make('zonas_id')
                                ->label('Zona')
                                ->placeholder('Selecciona una zona')
                                // ->required()
                                ->searchable()
                                ->options(
                                    fn(callable $get) =>
                                    Zonas::where('regiones_id', $get('regiones_id'))
                                        ->whereIn('id', function ($query) {
                                            $query->select('id')
                                                ->from('zonas');
                                        })
                                        ->pluck('nombre_zona', 'id')
                                )

                                ->reactive()
                                ->disabled(fn(callable $get) => empty($get('regiones_id'))),

                            Section::make('Fotos del establecimiento')
                                ->schema([
                                    FileUpload::make('front_image')
                                        ->label('Foto Exterior')
                                        ->helperText('Carga una foto del exterior del establecimiento')
                                        ->image()
                                        ->required()
                                        ->imageEditor()
                                        ->directory('customer-images'),

                                    FileUpload::make('inside_image')
                                        ->label('Foto Interior')
                                        ->helperText('Carga una foto del interior del establecimiento')
                                        ->image()
                                        ->required()
                                        ->imageEditor()
                                        ->directory('customer-images'),
                                ])->columns(2)->icon('heroicon-o-camera'),

                            Section::make('Informacion adicional')
                                ->collapsed()
                                ->schema([
                                    MarkdownEditor::make('extra')
                                        //  ->required()
                                        ->label('Datos extra del cliente')
                                ])->icon('heroicon-o-information-circle')
                        ])->columns(2),

                    Step::make('Fiscales')
                        ->description('Datos de facturacion')
                        ->schema([
                            Section::make('Cliente con facturacion')
                                ->description('Despliega unicamente si el cliente cuenta con datos de facturacion')
                                ->schema([
                                    TextInput::make('name_facturacion')
                                        ->label('Nombre')
                                        //   ->required()
                                        ->suffixIcon('heroicon-m-user-circle'),

                                    TextInput::make('razon_social')
                                        ->label('Razon Social')
                                        //   ->required()
                                        ->suffixIcon('heroicon-m-building-library'),

                                    TextInput::make('address_facturacion')
                                        ->label('Direccion')
                                        //   ->required()
                                        ->suffixIcon('heroicon-m-map-pin'),

                                    TextInput::make('postal_code_facturacion')
                                        ->label('Codigo Postal')
                                        ->numeric()
                                        //   ->required()
                                        ->suffixIcon('heroicon-m-hashtag'),

                                    Select::make('tipo_cfdi')
                                        ->label('Tipo de CFDI')
                                        ->options([
                                            'Ingreso' => CfdiTypeEnum::INGRESO->value,
                                            'Egreso' => CfdiTypeEnum::EGRESO->value,
                                            'Traslado' => CfdiTypeEnum::TRASLADO->value,
                                            'Nomina' => CfdiTypeEnum::NOMINA->value
                                        ])
                                        ->suffixIcon('heroicon-m-document-text'),

                                    Select::make('tipo_razon_social')
                                        ->label('Tipo de Razon Social')
                                        ->options([
                                            'Sociedad Anonima' => SociedadTypeEnum::S_ANONIMA->value,
                                            'Sociedad Civil' => SociedadTypeEnum::S_CIVIL->value,
                                        ])
                                        ->suffixIcon('heroicon-m-document-text'),

                                    FileUpload::make('cfdi_document')
                                        ->columnSpanFull()
                                        ->label('CFDI')
                                        ->helperText('Carga un CFDI en formato PDF')
                                        //   ->required()
                                        ->directory('customer-cfdi')
                                ])->collapsed()

                        ])->columns(2),
                ])->columnSpanFull()
                //->startOnStep(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        // Hide table from Resource
        return $table
            ->columns([])
            ->content(null)
            ->paginated(false);
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('tipo_cliente', ['PV', 'BL', 'SL', 'RD'])->count();
    }
}
