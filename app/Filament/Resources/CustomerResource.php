<?php

namespace App\Filament\Resources;

use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use App\Enums\CfdiTypeEnum;
use App\Enums\SociedadTypeEnum;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Forms\Components\GoogleMaps;
use App\Models\Colonias;
use App\Models\Customer;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Paises;
use Carbon\Callback;
use DeepCopy\Filter\Filter;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
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
    protected static ?string $navigationGroup = 'Clientes y Prospectos';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $breadcrumb = "Clientes";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Basicos')
                        ->description('Informacion Personal')
                        ->schema([
                            Section::make('Datos personales')->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label('Registrado por:')
                                    ->required(),

                                TextInput::make('name')
                                    ->label('Nombre completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->suffixIcon('heroicon-m-user'),
    
                                TextInput::make('alias')
                                    ->label('Alias')
                                    //->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->suffixIcon('heroicon-m-user-circle'),
    
                                TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->suffixIcon('heroicon-m-at-symbol'),
    
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->suffixIcon('heroicon-m-phone'),
    
                                DatePicker::make('birthday')
                                    ->label('Fecha de nacimiento')
                                    ->suffixIcon('heroicon-m-cake')
                                    ->required(),

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
                                ])
                                ->default('PV')
                                ->colors([
                                    'PV' => 'primary',
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

                                Toggle::make('reventa')->label('Maneja Reventa?')->default(false)
                                    ->onIcon('heroicon-m-check')
                                    ->offIcon('heroicon-m-x-mark')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false),

                            ])->columns(2)
                        ])->columns(2),

                    Step::make('Negocio')
                        ->description('Informacion del establecimiento')
                        ->schema([
                            /*
                            Select::make('paises_id')
                                ->label('País')
                                ->options(Paises::pluck('nombre', 'id'))
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('estados_id', null);
                                    $set('municipios_id', null);
                                    $set('colonias_id', null);
                                }),

                            Select::make('estados_id')
                                ->label('Estado')
                                ->options(function ($get) {
                                    return Estados::where('paises_id', $get('paises_id'))
                                        ->pluck('nombre', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->disabled(function ($get) {
                                    return !$get('paises_id');
                                })
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('municipios_id', null);
                                    $set('colonias_id', null);
                                }),

                            Select::make('municipios_id')
                                ->label('Municipio')
                                ->options(function ($get) {
                                    return Municipios::where('estados_id', $get('estados_id'))
                                        ->pluck('nombre', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->disabled(function ($get) {
                                    return !$get('estados_id');
                                })
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('colonias_id', null);
                                }),

                            Select::make('colonias_id')
                                ->label('Colonia')
                                ->options(function ($get) {
                                    return Colonias::where('municipios_id', $get('municipios_id'))
                                        ->pluck('nombre', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->disabled(function ($get) {
                                    return !$get('municipios_id');
                                }),
                        */
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
                                ->reverseGeocode([
                                    'street' => '%n %S',
                                    'city' => '%L',
                                    'state' => '%A1',
                                    'zip' => '%z',
                                ])
                                ->defaultZoom(15)
                                ->draggable()
                                ->autocomplete('full_address')
                                ->autocompleteReverse(true)
                                ->defaultLocation(fn ($record) => [
                                    $record->latitude ?? 19.8386943,
                                    $record->longitude ?? -90.4982317,
                                ])
                                ->columnSpanFull()->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                }),

                            TextInput::make('latitude')
                                ->label('Latitud')
                                ->helperText('Formato: 20.1845751')
                                ->unique(ignoreRecord: true)
                                //   ->required()
                                ->maxLength(100)
                                ->suffixIcon('heroicon-m-map-pin')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('location', [
                                        'lat' => floatVal($state),
                                        'lng' => floatVal($get('longitude')),
                                    ]);
                                })->lazy(),

                            TextInput::make('longitude')
                                ->label('Longitud')
                                ->helperText('Formato: 20.1845751')
                                ->unique(ignoreRecord: true)
                                //   ->required()
                                ->maxLength(100)
                                ->suffixIcon('heroicon-m-map-pin')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('location', [
                                        'lat' => floatval($get('latitude')),
                                        'lng' => floatVal($state),
                                    ]);
                                })->lazy(),

                            Section::make('Fotos del establecimiento')
                                ->collapsed()
                                ->schema([
                                    FileUpload::make('front_image')
                                        ->label('Foto Exterior')
                                        ->helperText('Carga una foto del exterior del establecimiento')
                                        ->image()
                                        //  ->required()
                                        ->imageEditor()
                                        ->directory('customer-images'),

                                    FileUpload::make('inside_image')
                                        ->label('Foto Interior')
                                        ->helperText('Carga una foto del interior del establecimiento')
                                        ->image()
                                        //   ->required()
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
        /*
        return $table
            ->heading('Clientes')
            ->description('Gestion de clientes.')
            ->columns([
                ImageColumn::make('avatar')->searchable(),
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('user.name')->label('Registrado por:')->searchable(),
                TextColumn::make('birthday')->label('Fecha de nacimiento')
                    ->date()->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('alias')->label('Alias')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')->label('Correo')->searchable()->badge()->color('info'),
                TextColumn::make('phone')->label('Telefono')->searchable()->badge()->color('success'),
                TextColumn::make('paises.nombre')->label('Pais')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('estados.nombre')->label('Estado')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('municipios.nombre')->label('Municipios')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('full_address')->label('Direccion')->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('latitude')->label('Ubicacion')
                    ->url(fn(Customer $record): string => "http://maps.google.com/maps?q=loc: {$record->latitude},{$record->longitude}")
                    ->openUrlInNewTab()->alignCenter() ->icon('heroicon-o-map-pin')->searchable(),
                ToggleColumn::make('is_preferred')->label('Preferred')->sortable(),
                ToggleColumn::make('is_visible')->label('Visible')->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')->label('Activo')->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')->label('Fecha registro')->date()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                FiltersFilter::make('is_active')
                    ->query(fn(Builder $query): Builder => $query->where('is_active', true))
                    ->label('Activos')
                    ->toggle(),

                FiltersFilter::make('is_preferred')
                    ->query(fn(Builder $query): Builder => $query->where('is_preferred', true))
                    ->label('Clientes Preferred')
                    ->toggle()
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Cliente eliminado')
                                ->body('El Cliente ha sido eliminado  del sistema.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('danger')
                                ->color('danger')
                        )
                        ->modalHeading('Borrar Cliente')
                        ->modalDescription('Estas seguro que deseas eliminar este Cliente? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Registros eliminados')
                                ->body('Los registros seleccionados han sido eliminados.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('danger')
                                ->color('danger')
                        )
                        ->modalHeading('Borrar Clientes')
                        ->modalDescription('Estas seguro que deseas eliminar los Clientes seleccionados? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
                ]),
            ]);
            */
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class
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
}
