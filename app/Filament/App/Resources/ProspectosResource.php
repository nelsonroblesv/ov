<?php

namespace App\Filament\App\Resources;

use App\Enums\CfdiTypeEnum;
use App\Enums\SociedadTypeEnum;
use App\Filament\App\Resources\CustomerUserResource\Pages;
use App\Filament\App\Resources\ProspectosResource\Pages\CreateProspectos;
use App\Filament\App\Resources\ProspectosResource\Pages\EditProspectos;
use App\Filament\App\Resources\ProspectosResource\Pages\ListProspectos;
use App\Filament\Resources\ProspectosResource\Pages\ViewProspectos;
use App\Models\Customer;
use App\Models\PaquetesInicio;
use App\Models\Regiones;
use App\Models\Zonas;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProspectosResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationGroup = 'Clientes y Prospectos';
    protected static ?string $navigationLabel = 'Prospeccion';
    protected static ?string $breadcrumb = "Prospeccion";
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Ubicacion')
                        ->description('Informacion Basica')
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
                                ])
                                ->live(),

                            TextInput::make('name')
                                ->label('Nombre del lugar o identificador')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->extraInputAttributes(['onInput' => 'this.value = this.value.toUpperCase()'])
                                ->suffixIcon('heroicon-m-map-pin'),

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

                            TextInput::make('full_address')
                                ->label('Dirección')
                                ->helperText('Calle, Núm. Ext., Núm. Int., Colonia, CP, Municipio, Estado, Pais')
                                ->required()
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
                                ->required()
                                ->options(
                                    fn() =>
                                    Regiones::whereIn('id', function ($query) {
                                        $query->select('regiones_id')
                                            ->from('zonas')
                                            ->whereIn('id', function ($subquery) {
                                                $subquery->select('zonas_id')
                                                    ->from('zona_usuario')
                                                    ->where('users_id', auth()->id());
                                            });
                                    })->pluck('name', 'id')
                                )
                                ->reactive()
                                ->searchable(),

                            Select::make('zonas_id')
                                ->label('Zona')
                                ->placeholder('Selecciona una zona')
                                ->required()
                                ->searchable()
                                ->options(fn(callable $get) => Zonas::where('regiones_id', $get('regiones_id')) // Obtiene el valor de 'regiones_id'
                                    ->whereHas('users', fn($query) => $query->where('users.id', auth()->id())) // Filtra por el usuario autenticado
                                    ->pluck('nombre_zona', 'id'))
                                ->reactive()
                                ->disabled(fn(callable $get) => empty($get('regiones_id'))),


                            Section::make('Notas Generales')
                                ->description('Despliega para agregar notas adicionales')
                                ->collapsed()
                                ->schema([
                                    MarkdownEditor::make('extra')
                                        ->label('Extra')
                                        ->nullable()
                                        ->columnSpanFull()
                                ])
                                ->columnSpanFull()

                        ])->columns(2),

                    Step::make('Contacto')
                        ->description('Informacion adicional')
                        ->schema([
                            TextInput::make('email')
                                ->label('Correo Electrónico')
                                ->rules([
                                    'regex:/^[a-zA-Z0-9._%+-ñÑ]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                                ])
                                ->unique(ignoreRecord: true)
                                ->placeholder('ejemplo@dominio.com')
                                ->suffixIcon('heroicon-m-at-symbol'),

                            TextInput::make('phone')
                                ->label('Teléfono')
                                ->tel()
                                ->requiredIf('tipo_cliente', 'PR')
                                ->helperText(function (Get $get) {
                                    return $get('tipo_cliente') === 'PR'
                                        ? 'Este campo es obligatorio para Prospectos.'
                                        : null;
                                })
                                ->validationMessages([
                                    'required' => 'El campo Teléfono es obligatorio para registros tipo Prospecto.',
                                ])
                                ->unique(ignoreRecord: true)
                                ->maxLength(50)
                                ->suffixIcon('heroicon-m-phone'),

                            FileUpload::make('front_image')
                                ->label('Fotos del establecimiento')
                                ->placeholder('Tomar fotos o cargar desde galeria')
                                ->directory('prospectos-images')
                                ->required()
                                ->multiple()
                                //->maxSize(2048)
                                ->columnSpanFull()

                        ])->columns(2),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query
                    ->where('user_id', auth()->id())
                    ->whereIn('tipo_cliente', ['PO', 'PR'])
                    ->where('is_active', true);
            })
            ->heading('Mis Prospectos')
            ->description('Lista de prospectos registrados en el sistema.')
            ->defaultSort('name', 'ASC')
            ->columns([
                TextColumn::make('name')->label('Identificador')->searchable()->sortable(),
                TextColumn::make('tipo_cliente')->label('Tipo')->badge()
                    ->colors([
                        'danger' => 'PO',
                        'warning' => 'PR'
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'PO' => 'Posible',
                        'PR' => 'Prospecto',
                    ][$state] ?? 'Otro'),
                //TextColumn::make('user.name')->label('Alta por')->searchable()->sortable(),
                TextColumn::make('regiones.name')->label('Region')->searchable()->sortable(),
                TextColumn::make('zona.nombre_zona')->label('Zona')->searchable()->sortable(),
                TextColumn::make('zona.tipo_semana')->label('Semana')->searchable()->sortable()
                    ->badge()
                    ->colors([
                        'success' => 'PAR',
                        'danger' => 'NON',
                    ])
                    ->icons([
                        'heroicon-o-arrow-long-down' => 'PAR',
                        'heroicon-o-arrow-long-up' => 'NON',
                    ]),
                TextColumn::make('zona.dia_zona')->label('Dia')->searchable()->sortable()
                    ->badge()
                    ->colors([
                        'info' => 'Lun',
                        'warning' => 'Mar',
                        'danger' => 'Me',
                        'success' => 'Jue',
                        'custom_light_blue' => 'Vie',
                    ]),
                TextColumn::make('simbolo')->label('Simbolo')->badge()
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
                        'heroicon-o-exclamation-triangle' => 'NC',
                        'heroicon-o-sparkles' => 'EU',
                        'heroicon-o-home-modern' => 'SYB'
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
                TextColumn::make('full_address')->label('Direccion')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('email')->label('Correo')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')->label('Telefono')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')->label('Notas')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Registro')->dateTime()->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false)
            ]);
        /*
            ->content(null)
            ->paginated(false);
            */
    }


    public static function getPages(): array
    {
        return [
            'index' => ListProspectos::route('/'),
            'create' => CreateProspectos::route('/create'),
            'edit' => EditProspectos::route('/{record}/edit'),
            'view' => ViewProspectos::route('/{record}'),
        ];
    }
}
