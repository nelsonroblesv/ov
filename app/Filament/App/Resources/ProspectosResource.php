<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProspectosResource\Pages;
use App\Filament\App\Resources\ProspectosResource\RelationManagers;
use App\Filament\Resources\ProspectosResource\RelationManagers\NamesRelationManager;
use App\Models\Prospectos;
use App\Models\Regiones;
use App\Models\Services;
use App\Models\Zonas;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProspectosResource extends Resource
{
    protected static ?string $model = Prospectos::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationGroup = 'Clientes y Prospectos';
    protected static ?string $navigationLabel = 'Prospeccion';
    protected static ?string $breadcrumb = "Prospeccion";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Ubicacion')
                        ->description('Informacion Basica')
                        ->schema([
                            Hidden::make('user_id')->default(fn() => auth()->id()),

                            ToggleButtons::make('tipo_prospecto')
                                ->label('Tipo de Registro')
                                ->required()
                                ->inline()
                                ->options([
                                    'Posible' => 'Posible',
                                    'Prospecto' => 'Prospecto',
                                ])
                                ->default('Posible')
                                ->colors([
                                    'Posible' => 'danger',
                                    'Prospecto' => 'warning'
                                ])
                                ->icons([
                                    'Posible' => 'heroicon-o-map',
                                    'Prospecto' => 'heroicon-o-star'
                                ]),

                            TextInput::make('name')
                                ->label('Nombre del lugar o identificador')
                                ->required()
                                // ->disabledOn('edit')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->suffixIcon('heroicon-m-map-pin'),

                            Select::make('services')
                                ->label('Servicios')
                                ->placeholder('Selecciona uno o mas servicios')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                //->relationship('services', 'name'),
                                ->options(Services::pluck('name', 'name'))
                                ->suffixIcon('heroicon-m-sparkles'),

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
                                //->geoJson('zonas.geojson') // GeoJSON file, URL or JSON
                                //->geoJsonContainsField('geojson') // field to capture GeoJSON polygon(s) which contain the map marker


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

                            TextInput::make('latitude')
                                ->hidden()
                                ->label('Latitud')
                                ->helperText('Formato: 20.1845751')
                                ->unique(ignoreRecord: true)
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
                                ->hidden()
                                ->label('Longitud')
                                ->helperText('Formato: 20.1845751')
                                ->unique(ignoreRecord: true)
                                ->maxLength(100)
                                ->suffixIcon('heroicon-m-map-pin')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('location', [
                                        'lat' => floatval($get('latitude')),
                                        'lng' => floatVal($state),
                                    ]);
                                })->lazy(),

                            Select::make('regiones_id')
                                ->label('Region')
                                ->options(Regiones::pluck('name', 'id'))
                                ->required()
                                ->reactive(),

                            Select::make('zonas_id')
                                ->label('Zona')
                                ->required()->options(function (callable $get) {
                                    $regionId = $get('regiones_id');
                                    if (!$regionId) {
                                        return [];
                                    }
                                    return Zonas::where('regiones_id', $regionId)->pluck('nombre_zona', 'id');
                                })
                                ->reactive()
                                ->disabled(fn(callable $get) => empty($get('regiones_id'))),


                            Section::make('Notas Generales')
                                ->description('Despliega para agregar notas adicionales')
                                ->collapsed()
                                ->schema([
                                    MarkdownEditor::make('notes')
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
                                ->label('Correo electrónico')
                                ->email()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255)
                                ->suffixIcon('heroicon-m-at-symbol'),

                            TextInput::make('phone')
                                ->label('Teléfono')
                                ->tel()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50)
                                ->suffixIcon('heroicon-m-phone'),

                            FileUpload::make('fachada')
                                ->label('Foto de fachada')
                                ->image()
                                ->imageEditor()
                                ->directory('prospectos-images')
                                ->columnSpanFull()

                        ])->columns(2),
                ])->columnSpanFull()
                //->startOnStep(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->content(null)
            ->paginated(false);
        /*
            ->columns([
                TextColumn::make('name')->label('Identificador')->searchable()->sortable(),
                TextColumn::make('tipo_prospecto'),
                TextColumn::make('regiones.name')->label('Region')->sortable()->searchable(),
                TextColumn::make('zonas.nombre_zona')->label('Zona')->sortable()->searchable(),
                TextColumn::make('notes')->label('Notas'),
                TextColumn::make('full_address')->label('Ubicacion')->searchable(),

                IconColumn::make('reventa')->boolean(),TextColumn::make('user.name')->sortable(),
                
                TextColumn::make('created_at')->label('Fecha registro')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
            */
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspectos::route('/'),
            'create' => Pages\CreateProspectos::route('/create'),
            'edit' => Pages\EditProspectos::route('/{record}/edit'),
        ];
    }
}
