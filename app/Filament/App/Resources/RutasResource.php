<?php

namespace App\Filament\App\Resources;

use App\Enums\CfdiTypeEnum;
use App\Enums\SociedadTypeEnum;
use App\Filament\App\Resources\RutasResource\Pages;
use App\Filament\App\Resources\RutasResource\RelationManagers;
use App\Filament\Resources\PaquetesInicioResource;
use App\Models\AsignarTipoSemana;
use App\Models\BitacoraCustomers;
use App\Models\Customer;
use App\Models\PaquetesInicio;
use App\Models\Regiones;
use App\Models\Rutas;
use App\Models\Services;
use App\Models\User;
use App\Models\Zonas;
use Carbon\Carbon;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RutasResource extends Resource
{
    protected static ?string $model = Rutas::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Bitacora';
    protected static ?string $navigationLabel = 'Mis Rutas';
    protected static ?string $breadcrumb = "Mis Rutas";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->heading('Mis Rutas')
            ->description('Estas son tus Rutas programadas para hoy ' . Carbon::now()->setTimezone('America/Merida')->locale('es')->translatedFormat('l d \d\e F Y') .
                '. No olvides agregar un registro en la Bitacora durante cada visita.')
            ->reorderable('sort')
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->id();

                $tipoSemanaSeleccionado = AsignarTipoSemana::value('tipo_semana');
                $valores = [
                    '0' => 'PAR',
                    '1' => 'NON',
                ];
                $semana = $valores[$tipoSemanaSeleccionado];
                $query->where('user_id', $user)
                    ->where('visited', '0')
                    ->where('tipo_semana', $semana);
            })
            ->defaultSort('sort', 'desc')
            ->headerActions([

                /************ NUEVO CLIENTE  ****************/
                Action::make('Registrar Cliente')
                    ->label('Nuevo Cliente')
                    ->icon('heroicon-m-user-plus')
                    ->color('success')
                    ->form([
                        Section::make('Información Personal')
                            ->description('Completa los campos con la información del nuevo cliente.')
                            ->schema([

                                Hidden::make('user_id')->default(fn() => auth()->id()),
                                Hidden::make('alta_user_id')->default(fn() => auth()->id()),

                                TextInput::make('name')
                                    ->label('Nombre completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->extraInputAttributes(['onInput' => 'this.value = this.value.toUpperCase()'])
                                    ->suffixIcon('heroicon-m-user'),

                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->required()
                                    ->rules([
                                        'regex:/^[a-zA-Z0-9._%+-ñÑ]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                                    ])
                                    ->unique(table: Customer::class, column: 'email', ignoreRecord: true)
                                    ->placeholder('ejemplo@dominio.com')
                                    ->suffixIcon('heroicon-m-at-symbol'),

                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->unique(table: Customer::class, column: 'phone', ignoreRecord: true)
                                    ->maxLength(50)
                                    ->suffixIcon('heroicon-m-phone'),

                                Select::make('simbolo')
                                    ->required()
                                    ->label('Simbologia')
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
                                    ->suffixIcon('heroicon-m-cake'),
                                //->required(),

                            ])->columns(2)->icon('heroicon-o-information-circle'),

                        Section::make('Sistema')
                            ->description('Selecciona el tipo de cliente')
                            ->schema([
                                ToggleButtons::make('tipo_cliente')
                                    ->label('Tipo de Cliente')
                                    ->inline()
                                    ->options([
                                        'PV' => 'Punto Venta',
                                        'RD' => 'Red',
                                        'BK' => 'Black',
                                        'SL' => 'Silver',
                                    ])
                                    ->default('PV')
                                    ->colors([
                                        'PV' => 'success',
                                        'RD' => 'danger',
                                        'BK' => 'info',
                                        'SL' => 'warning'
                                    ])
                                    ->icons([
                                        'PV' => 'heroicon-o-building-storefront',
                                        'RD' => 'heroicon-o-user',
                                        'BK' => 'heroicon-o-star',
                                        'SL' => 'heroicon-o-sparkles'
                                    ]),
                                Select::make('paquete_inicio_id')
                                    ->label('Paquete de inicio')
                                    ->options(
                                        PaquetesInicio::where('activo', 1)
                                            ->orderByRaw("CASE 
                                                    WHEN prefijo = 'paquete' THEN 1 
                                                    WHEN prefijo = 'barber' THEN 2 
                                                    ELSE 3 
                                                  END, precio DESC") // Agrupa primero 'paquete', luego 'barber', luego los demás. Dentro de cada grupo, ordena por precio DESC.
                                            ->get()
                                            ->mapWithKeys(function ($paquete) {
                                                return [
                                                    $paquete->id => "{$paquete->prefijo} {$paquete->nombre} ({$paquete->precio} MXN)"
                                                ];
                                            })
                                    )
                                    ->placeholder('Selecciona un paquete')
                                    ->required(),
                            ])->icon('heroicon-o-computer-desktop'),

                        Section::make('Ubicación')
                            ->description('Ingresa la ubicación del cliente')
                            ->schema([
                                TextInput::make('full_address')
                                    ->label('Dirección')
                                    ->helperText('Calle, Núm. Ext., Núm. Int., Colonia, Intersecciones')
                                    ->required()
                                    ->maxLength(255)
                                    ->suffixIcon('heroicon-m-map')
                                    ->columnSpanFull(),

                                Map::make('location')
                                    ->mapControls([
                                        'mapTypeControl'    => true,
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
                                    ->required()
                                    ->options(
                                        fn() =>
                                        Regiones::whereIn('id', function ($query) {
                                            $query->select('regiones_id')
                                                ->from('zonas')
                                                ->where('user_id', auth()->id());
                                        })->pluck('name', 'id')
                                    )
                                    ->reactive(),

                                Select::make('zonas_id')
                                    ->label('Zona')
                                    ->placeholder('Selecciona una zona')
                                    ->required()
                                    ->searchable()
                                    ->options(
                                        fn(callable $get) =>
                                        Zonas::where('regiones_id', $get('regiones_id'))
                                            ->whereIn('id', function ($query) {
                                                $query->select('id')
                                                    ->from('zonas')
                                                    ->where('user_id', auth()->id());
                                            })
                                            ->pluck('nombre_zona', 'id')
                                    )
                                    ->reactive()
                                    ->disabled(fn(callable $get) => empty($get('regiones_id'))),
                            ])->columns(2)->icon('heroicon-o-map-pin'),

                        Section::make('Fotos del establecimiento')
                            ->schema([
                                FileUpload::make('front_image')
                                    ->label('Fotos de Exterior')
                                    ->placeholder('Tomar fotos o cargar desde galeria')
                                    ->multiple()
                                    ->image()
                                    ->imageEditor()
                                    ->directory('customer-images')
                                    ->required()
                                    ->columnSpanFull(),

                                FileUpload::make('inside_image')
                                    ->label('Fotos de Interior')
                                    ->placeholder('Tomar fotos o cargar desde galeria')
                                    ->multiple()
                                    ->image()
                                    ->imageEditor()
                                    ->directory('customer-images')
                                    ->required()
                                    ->columnSpanFull(),
                            ])->columns(2)->icon('heroicon-o-camera'),
                    ])
                    ->action(function (array $data) {
                        $data['created_at'] = Carbon::now()->setTimezone('America/Merida');
                        $customer = Customer::create($data);

                        Rutas::create([
                            'user_id'  => auth()->id(),
                            'customer_id' => $customer->id,
                            'regiones_id' => $data['regiones_id'],
                            'regiones_id' => $data['regiones_id'],
                            'zonas_id' => $data['zonas_id'],
                            'tipo_semana' => Zonas::find($data['zonas_id'])->tipo_semana,
                            'tipo_cliente' => $data['tipo_cliente'],
                            'full_address' => $data['full_address'],
                            'created_at' => Carbon::now()->setTimezone('America/Merida')->toDateString(),
                            'visited' => 0
                        ]);


                        Notification::make()
                            ->title('Cliente registrado')
                            ->body('El Cliente ha sido registrado con éxito. Se agregó a la Ruta actual. 
                                        Recuerda completar la información adicional posteriormente.')
                            ->icon('heroicon-o-user-plus')
                            ->color('success')
                            ->send();
                    }),


                /**************** NUEVO PRSPECTO/POSIBLE  *************/
                Action::make('Registrar Prospección')
                    ->label('Nueva Prospección')
                    ->icon('heroicon-m-magnifying-glass-plus')
                    ->color('warning')
                    ->form([
                        Section::make('Información del establecimiento')
                            ->description('Completa los campos con la información requerida.')
                            ->schema([

                                Hidden::make('user_id')->default(fn() => auth()->id()),
                                Hidden::make('alta_user_id')->default(fn() => auth()->id()),

                                ToggleButtons::make('tipo_cliente')
                                    ->label('Tipo de Registro')
                                    ->required()
                                    ->inline()
                                    ->options([
                                        'PO' => 'Posible',
                                        'PR' => 'Prospecto',
                                    ])
                                    ->default('PO')
                                    ->colors([
                                        'PO' => 'danger',
                                        'PR' => 'warning'
                                    ])
                                    ->icons([
                                        'PO' => 'heroicon-o-map',
                                        'PR' => 'heroicon-o-star'
                                    ]),

                                TextInput::make('name')
                                    ->label('Nombre completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->suffixIcon('heroicon-m-user'),

                                Select::make('services')
                                    ->label('Servicios')
                                    ->placeholder('Selecciona uno o mas servicios')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->options(Services::pluck('name', 'name'))
                                    ->suffixIcon('heroicon-m-sparkles'),

                                Select::make('simbolo')
                                    ->label('Simbologia')
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

                                Toggle::make('reventa')->label('Ya maneja Reventa')->default(false)
                                    ->onIcon('heroicon-m-check')
                                    ->offIcon('heroicon-m-x-mark')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(true),

                            ])->columns(2)->icon('heroicon-o-information-circle'),

                        Section::make('Datos de contacto')
                            ->description('Ingresa la información de contacto.')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->rules([
                                        'regex:/^[a-zA-Z0-9._%+-ñÑ]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                                    ])
                                    ->unique(table: Customer::class, column: 'email', ignoreRecord: true)
                                    ->placeholder('ejemplo@dominio.com')
                                    ->suffixIcon('heroicon-m-at-symbol'),

                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->unique(table: Customer::class, column: 'phone', ignoreRecord: true)
                                    ->maxLength(50)
                                    ->suffixIcon('heroicon-m-phone')
                            ])->columns(2)->icon('heroicon-o-identification'),

                        Section::make('Fachada del establecimiento')
                            ->schema([
                                FileUpload::make('front_image')
                                    ->label('Fotos del establecimiento')
                                    ->required()
                                    ->placeholder('Tomar fotos o cargar desde galeria')
                                    ->multiple()
                                    ->image()
                                    ->imageEditor()
                                    ->directory('prospectos-images')
                                    ->columnSpanFull()
                            ])->icon('heroicon-o-camera'),

                        Section::make('Ubicación')
                            ->description('Ingresa la ubicación del cliente')
                            ->schema([
                                TextInput::make('full_address')
                                    ->label('Dirección')
                                    ->helperText('Calle, Núm. Ext., Núm. Int., Colonia, Intersecciones')
                                    ->required()
                                    ->maxLength(255)
                                    ->suffixIcon('heroicon-m-map')
                                    ->columnSpanFull(),

                                Map::make('location')
                                    ->mapControls([
                                        'mapTypeControl'    => true,
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
                                    ->required()
                                    ->options(
                                        fn() =>
                                        Regiones::whereIn('id', function ($query) {
                                            $query->select('regiones_id')
                                                ->from('zonas')
                                                ->where('user_id', auth()->id());
                                        })->pluck('name', 'id')
                                    )
                                    ->reactive(),

                                Select::make('zonas_id')
                                    ->label('Zona')
                                    ->placeholder('Selecciona una zona')
                                    ->required()
                                    ->searchable()
                                    ->options(
                                        fn(callable $get) =>
                                        Zonas::where('regiones_id', $get('regiones_id'))
                                            ->whereIn('id', function ($query) {
                                                $query->select('id')
                                                    ->from('zonas')
                                                    ->where('user_id', auth()->id());
                                            })
                                            ->pluck('nombre_zona', 'id')
                                    )
                                    ->reactive()
                                    ->disabled(fn(callable $get) => empty($get('regiones_id'))),
                            ])->columns(2)->icon('heroicon-o-map-pin'),

                        Section::make('Notas')
                            ->description('Despliega para agregar notas adicionales')
                            ->collapsed()
                            ->schema([
                                MarkdownEditor::make('extra')
                                    ->label('Extra')
                                    ->nullable()
                                    ->columnSpanFull()
                            ])->columnSpanFull()->icon('heroicon-o-pencil-square')
                    ])
                    ->action(function (array $data) {
                        $data['created_at'] = Carbon::now()->setTimezone('America/Merida');
                        $customer = Customer::create($data);

                        Rutas::create([
                            'user_id'  => auth()->id(),
                            'customer_id' => $customer->id,
                            'regiones_id' => $data['regiones_id'],
                            'zonas_id' => $data['zonas_id'],
                            'tipo_semana' => Zonas::find($data['zonas_id'])->tipo_semana,
                            'tipo_cliente' => $data['tipo_cliente'],
                            'full_address' => $data['full_address'],
                            'created_at' => Carbon::now()->setTimezone('America/Merida')->toDateString(),
                            'visited' => 0
                        ]);

                        Notification::make()
                            ->title('Prospección registrada')
                            ->body('Se ha registrado la Prospección con éxito y se agregó a la Ruta actual. 
                                    Recuerda completar la información adicional posteriormente.')
                            ->icon('heroicon-o-magnifying-glass')
                            ->color('success')
                            ->send();
                    }),
            ])
            ->columns([
                TextColumn::make('sort')->label('#')->sortable(),
                TextColumn::make('customer.name')->label('Cliente o Identificador'),
                IconColumn::make('customer.phone')->label('WhatsApp')->alignCenter()
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('success')
                    ->url(fn($record) => "https://wa.me/" . urlencode($record->customer->phone), true)
                    ->openUrlInNewTab(),
                TextColumn::make('customer.simbolo')->label('Simbolo')->badge()
                    ->colors([
                        'black',/*
					'custom' => 'SB',
					'success' => 'BB', 
					'success' => 'UN', 
					'success' => 'OS', 
					'success' => 'CR', 
					'success' => 'UB', 
					'success' => 'NC'*/
                    ])
                    ->icons([
                        'heroicon-o-scissors' => 'SB',
                        'heroicon-o-building-storefront' => 'BB',
                        'heroicon-o-hand-raised' => 'UN',
                        'heroicon-o-rocket-launch' => 'OS',
                        'heroicon-o-x-mark' => 'CR',
                        'heroicon-o-map-pin' => 'UB',
                        'heroicon-o-exclamation-triangle' => 'NC'
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'SB' => 'Salón de Belleza',
                        'SYB' => 'Salón y Barbería',
                        'EU' => 'Estética Unisex',
                        'BB' => 'Barbería',
                        'UN' => 'Salón de Uñas',
                        'OS' => 'OSBERTH',
                        'CR' => 'Cliente Pedido Rechazado',
                        'UB' => 'Ubicación en Grupo',
                        'NC' => 'Ya no compran'
                    ][$state] ?? 'Otro'),
                TextColumn::make('tipo_cliente')->label('Tipo')->badge()->alignCenter()
                    ->colors([
                        'gray' => 'PO',
                        'warning' => 'PR',
                        'success' => 'PV',
                        'danger' => 'RD',
                        'info' => 'BK',
                        'warning' => 'SL'
                    ])
                    ->icons([
                        'heroicon-o-map' => 'PO',
                        'heroicon-o-magnifying-glass' => 'PR',
                        'heroicon-o-building-storefront' => 'PV',
                        'heroicon-o-user' => 'RD',
                        'heroicon-o-star' => 'BK',
                        'heroicon-o-sparkles' => 'SL'
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'PO' => 'Posible',
                        'PR' => 'Prospecto',
                        'PV' => 'Punto Venta',
                        'RD' => 'Red',
                        'BK' => 'Black',
                        'SL' => 'Silver',
                    ][$state] ?? 'Otro'),
                TextColumn::make('regiones.name')->label('Region')
                    ->badge()
                    ->alignCenter()
                    ->colors(['info']),
                TextColumn::make('zonas.nombre_zona')->label('Zona')
                    ->badge()
                    ->alignCenter()
                    ->colors(['warning']),
                TextColumn::make('full_address')->label('Direccion'),
                IconColumn::make('full_address')->label('Ubicación')->alignCenter()
                    ->icon('heroicon-o-map-pin')
                    ->color('danger')
                    ->url(fn($record) => "https://www.google.com/maps/search/?api=1&query=" . urlencode($record->full_address), true)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionsActionGroup::make([
                    Action::make('Registrar Visita')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->color('warning')
                        ->form([
                            Section::make('Información de la Visita')->schema([

                                // Select para elegir el tipo de visita
                                Select::make('tipo_visita')
                                    ->placeholder('Seleccione una opción') // Placeholder no seleccionable
                                    ->required()
                                    ->label('Tipo de Visita')
                                    ->options([
                                        'entrega' => 'Entrega de Pedido',
                                        'cerrado' => 'Establecimiento Cerrado',
                                        'regular' => 'Visita Regular',
                                        'prospectacion' => 'Prospectación',
                                    ])
                                    ->reactive()
                                    ->default('entrega')
                                    ->columnSpanFull(),

                                // Sección de Entrega de Pedido
                                Section::make('Entrega de Pedido')
                                    ->visible(fn($get) => $get('tipo_visita') === 'entrega')
                                    ->schema([
                                        FileUpload::make('foto_entrega')
                                            ->label('Foto de entrega')
                                            ->nullable()
                                            ->placeholder('Foto de entrega de pedido')
                                            ->multiple()
                                            ->directory('fotos-bitacora')
                                            ->required(),

                                        FileUpload::make('foto_stock_antes')
                                            ->label('Foto de stock antes')
                                            ->placeholder('Foto de stock antes de entrega')
                                            ->multiple()
                                            ->directory('fotos-bitacora')
                                            ->required(),

                                        FileUpload::make('foto_stock_despues')
                                            ->label('Foto de stock después')
                                            ->placeholder('Foto de stock después de entrega')
                                            ->multiple()
                                            ->directory('fotos-bitacora')
                                            ->required(),
                                    ]),

                                // Sección de Establecimiento Cerrado
                                Section::make('Establecimiento Cerrado')
                                    ->visible(fn($get) => $get('tipo_visita') === 'cerrado')
                                    ->schema([
                                        FileUpload::make('foto_lugar_cerrado')
                                            ->label('Foto de establecimiento cerrado')
                                            ->placeholder('Tomar o cargar foto')
                                            ->multiple()
                                            ->directory('fotos-bitacora')
                                            ->required(),
                                    ]),

                                // Sección de Visita Regular
                                Section::make('Visita Regular')
                                    ->visible(fn($get) => $get('tipo_visita') === 'regular')
                                    ->schema([
                                        FileUpload::make('foto_stock_regular')
                                            ->label('Foto de stock actual')
                                            ->placeholder('Tomar o cargar foto')
                                            ->multiple()
                                            ->directory('fotos-bitacora')
                                            ->required(),
                                    ]),

                                // Sección de Prospectación
                                Section::make('Prospectación')
                                    ->visible(fn($get) => $get('tipo_visita') === 'prospectacion')
                                    ->schema([
                                        Toggle::make('show_video')
                                            ->label('Se presentó Video Testimonio')
                                            ->onIcon('heroicon-m-play')
                                            ->offIcon('heroicon-m-x-mark')
                                            ->onColor('success')
                                            ->offColor('danger'),

                                        FileUpload::make('foto_evidencia_prospectacion')
                                            ->label('Fotos de Evidencia')
                                            ->placeholder('Tomar o carga foto')
                                            //->multiple()
                                            ->directory('bitacora-testigos')
                                            ->required(),
                                    ]),

                                // Notas generales
                                TextInput::make('notas')
                                    ->label('Notas')
                                    ->required()
                                    ->columnSpanFull(),

                            ])
                        ])
                        ->hidden(function ($record) {
                            return BitacoraCustomers::where('user_id', auth()->id())
                                ->where('customers_id', $record->customer_id)
                                ->whereDate('created_at', Carbon::now()->setTimezone('America/Merida')->toDateString())
                                ->exists();
                        })
                        ->action(function ($record, array $data) {
                            BitacoraCustomers::create([

                                'customers_id' => $record->customer_id,
                                'user_id' => auth()->id(),
                                'show_video' => $data['show_video'] ?? null,

                                'tipo_visita' => $data['tipo_visita'],

                                'foto_entrega' => isset($data['foto_entrega']) ? (is_array($data['foto_entrega']) ? $data['foto_entrega'][0] : $data['foto_entrega']) : null,
                                'foto_stock_antes' => isset($data['foto_stock_antes']) ? (is_array($data['foto_stock_antes']) ? $data['foto_stock_antes'][0] : $data['foto_stock_antes']) : null,
                                'foto_stock_despues' => isset($data['foto_stock_despues']) ? (is_array($data['foto_stock_despues']) ? $data['foto_stock_despues'][0] : $data['foto_stock_despues']) : null,
                                'foto_lugar_cerrado' => isset($data['foto_lugar_cerrado']) ? (is_array($data['foto_lugar_cerrado']) ? $data['foto_lugar_cerrado'][0] : $data['foto_lugar_cerrado']) : null,
                                'foto_stock_regular' => isset($data['foto_stock_regular']) ? (is_array($data['foto_stock_regular']) ? $data['foto_stock_regular'][0] : $data['foto_stock_regular']) : null,
                                'foto_evidencia_prospectacion' => isset($data['foto_evidencia_prospectacion']) ? (is_array($data['foto_evidencia_prospectacion']) ? $data['foto_evidencia_prospectacion'][0] : $data['foto_evidencia_prospectacion']) : null,


                                'notas' => $data['notas'],

                                'created_at' => Carbon::now()->setTimezone('America/Merida')
                            ]);

                            $record->update(['visited' => 1]);

                            Notification::make()
                                ->title('Registro guardado en la Bitácora')
                                ->success()
                                ->send();
                        }),

                    Action::make('transferir')
                        ->label('Transferir')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrows-up-down')
                        ->color('info')
                        ->modalHeading('Transferir a Cliente')
                        ->visible(fn($record) => $record->tipo_cliente === 'PR') // Solo para PR
                        ->modalDescription('Para el proceso de transferencia es necesario completar toda la 
                                    informacion que se pide a continuacion.')

                        ->fillForm(fn(Rutas $record) => [
                            'customer_id' => $record->customer_id,
                            'name' => $record->customer?->name,
                            'phone' => $record->customer?->phone,
                            'email' => $record->customer?->email,
                        ])
                        ->form([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->required(),

                            TextInput::make('phone')
                                ->label('Teléfono')
                                ->required(),

                            TextInput::make('email')
                                ->label('Correo Electrónico')
                                ->email()
                                ->required(),

                            DatePicker::make('birthday')
                                ->label('Fecha de nacimiento')
                                ->suffixIcon('heroicon-m-cake')
                                ->required(),

                            Select::make('paquete_inicio_id')
                                ->label('Paquete de inicio')
                                ->options(PaquetesInicio::pluck('nombre', 'id'))
                                ->placeholder('Selecciona un paquete')
                        ])
                        ->action(function (array $data, Rutas $record) {
                            $customer = Customer::find($record->customer_id);

                            if ($customer) {
                                $customer->update([
                                    'phone' => $data['phone'],
                                    'email' => $data['email'],
                                    'birthday' => $data['birthday'],
                                    'tipo_cliente' => 'PV',
                                    'paquete_inicio_id' => $data['paquete_inicio_id'],
                                ]);

                                $record->update(['customer_id' => $customer->id]);
                            }

                            $record->update([
                                'tipo_cliente' => 'PV',
                            ]);

                            Notification::make()
                                ->title('Prospecto Transferido')
                                ->body('El Prospecto ha sido transferido a la lista de Clientes.')
                                ->icon('heroicon-o-information-circle')
                                ->color('info')
                                ->send();

                            $recipient = User::where('role', 'Administrador')->get();
                            $username =  User::find($record['user_id'])->name;

                            Notification::make()
                                ->title('Prospecto transferido')
                                ->body('El vendedor ' . $username . ' ha transferido a ' . $record->customer->name . ' como nuevo cliente Punto de Venta.')
                                ->icon('heroicon-o-information-circle')
                                ->iconColor('info')
                                ->color('info')
                                ->sendToDatabase($recipient);
                        })



                        ->hidden(fn($record) => in_array($record->tipo_cliente, ['PV', 'RD', 'BK', 'SL']))


                ])
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListRutas::route('/'),
            'create' => Pages\CreateRutas::route('/create'),
            'edit' => Pages\EditRutas::route('/{record}/edit'),
        ];
    }
}
