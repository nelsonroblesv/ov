<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectosResource\Pages;
use App\Filament\Resources\ProspectosResource\RelationManagers;
use App\Filament\Resources\ProspectosResource\RelationManagers\NamesRelationManager;
use App\Filament\Resources\ProspectosResource\Widgets\ProspectosMapWidget;
use App\Models\Customer;
use App\Models\Prospectos;
use App\Models\Regiones;
use App\Models\Services;
use App\Models\User;
use App\Models\Zonas;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\table;

class ProspectosResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationGroup = 'Clientes & Prospectos';
    protected static ?string $navigationLabel = 'Prospeccion';
    protected static ?string $breadcrumb = "Prospeccion";
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Ubicacion')
                        ->description('Informacion Basica')
                        ->schema([

                            Hidden::make('alta_user_id')->default(fn() => auth()->id()),

                            Select::make('user_id')
                                ->required()
                                ->label('Asignado a:')
                                ->options(
                                    fn() =>
                                    User::whereIn('id', function ($query) {
                                        $query->select('id')
                                            ->from('users')
                                            ->where('role', 'Vendedor')
                                            ->orWhere('username', 'OArrocha')
                                            ->orderBy('name', 'DESC');
                                    })->pluck('name', 'id')
                                ),


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
                                ->searchable()
                                ->options(Regiones::pluck('name', 'id'))
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
                                                ->from('zonas');
                                        })
                                        ->pluck('nombre_zona', 'id')
                                ),

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
                                ->unique(ignoreRecord: true)
                                ->rules([
                                    'regex:/^[a-zA-Z0-9._%+-ñÑ]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                                ])
                                ->placeholder('ejemplo@dominio.com')
                                ->suffixIcon('heroicon-m-at-symbol'),

                            TextInput::make('phone')
                                ->label('Teléfono')
                                ->tel()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50)
                                ->suffixIcon('heroicon-m-phone'),

                            FileUpload::make('front_image')
                                ->label('Foto de Fachada')
                                ->helperText('Carga una foto del exterior del establecimiento')
                                ->required()
                                ->imageEditor()
                                ->directory('customer-images')
                                ->columnSpanFull(),

                        ])->columns(2),
                ])->columnSpanFull()
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->content(null)
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspectos::route('/'),
            'create' => Pages\CreateProspectos::route('/create'),
            'edit' => Pages\EditProspectos::route('/{record}/edit'),
            'view' => Pages\ViewProspectos::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('tipo_cliente', ['PO', 'PR'])->count();
    }
}
