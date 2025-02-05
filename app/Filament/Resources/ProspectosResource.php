<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectosResource\Pages;
use App\Filament\Resources\ProspectosResource\RelationManagers;
use App\Filament\Resources\ProspectosResource\RelationManagers\NamesRelationManager;
use App\Filament\Resources\ProspectosResource\Widgets\ProspectosMapWidget;
use App\Models\Colonias;
use App\Models\Customer;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Paises;
use App\Models\Prospectos;
use App\Models\Services;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
    protected static ?string $model = Prospectos::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationGroup = 'Clientes y Prospectos';
    protected static ?string $navigationLabel = 'Prospeccion';
    protected static ?string $breadcrumb = "Prospeccion";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Ubicacion')
                        ->description('Informacion Basica')
                        ->schema([
                            Select::make('user_id')
                                ->required()
                                ->relationship('user', 'name')
                                ->label('Registrado por:'),

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
                                ->suffixIcon('heroicon-m-user'),

                                Select::make('services')
                                ->label('Servicios')
                                ->multiple()
                                ->preload()
                                //->relationship('servicios', 'name')
                               // ->createOptionForm([
                               //     TextInput::make('name')
                                //        ->required()
                                //        ->label('Nombre del Servicio'),
                               // ]),
                                ,
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
                                ->defaultZoom(8)
                                ->autocomplete('full_address')
                                ->autocompleteReverse(true)
                                ->reverseGeocode([
                                    'street' => '%n %S',
                                    'city' => '%L',
                                    'state' => '%A1',
                                    'zip' => '%z',
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

                            TextInput::make('latitude')
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
        // Hide table from Resource
        return $table
            ->columns([])
            ->content(null)
            ->paginated(false);

        /*
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Alta por')->searchable()->sortable(),
                ToggleColumn::make('is_active')->label('Activo')->alignCenter()->sortable(),
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('email')->label('Correo')->searchable()->sortable()->badge()->color('warning'),
                TextColumn::make('phone')->label('Telefono')->searchable()->sortable()->badge()->color('success'),
                TextColumn::make('paises.nombre')->label('Pais')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('estados.nombre')->label('Estado')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('municipios.nombre')->label('Municipio')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('full_address')->label('Direccion')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('latitude')->label('Ubicacion')
                    ->url(fn(Prospectos $record): string => "http://maps.google.com/maps?q=loc: {$record->latitude},{$record->longitude}")
                    ->openUrlInNewTab()->alignCenter()->icon('heroicon-o-map-pin')->searchable(),
                TextColumn::make('notes')->label('Notas')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    Action::make('transfer')
                        ->label('Transferir')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrows-up-down')
                        ->color('info')
                        ->modalHeading('Transferir Prospecto')
                        ->modalDescription('Estas seguro que deseas transferir este Prospecto como Cliente? Esta acción no se puede deshacer.')
                        ->action(function (Prospectos $record) {
                            if ($record->is_active == 0) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Solo puedes transferir Prospectos con status Activo.')
                                    ->danger()
                                    ->color('danger')
                                    ->send();

                                return;
                            }
                            if (Customer::where('email', $record->email)->exists()) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('El correo electrónico indicado esta asociado con un Cliente existente.')
                                    ->danger()
                                    ->color('danger')
                                    ->send();

                                return;
                            }

                            $clienteData = $record->toArray();
                            unset($clienteData['id'], $clienteData['created_at'], $clienteData['updated_at']);
                            Customer::create($clienteData);
                            $record->delete();

                            Notification::make()
                                ->title('Prospecto transferido')
                                ->body('El prospecto ha sido transferido como Cliente.')
                                ->success()
                                ->send();
                        }),

                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Prospecto eliminado')
                                ->body('El Prospecto ha sido eliminado  del sistema.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('danger')
                                ->color('danger')
                        )
                        ->modalHeading('Borrar Prospecto')
                        ->modalDescription('Estas seguro que deseas eliminar este Prospecto? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Registros eliminados')
                                ->body('Los registros seleccionados han sido eliminados.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('danger')
                                ->color('danger')
                        )
                        ->modalHeading('Borrar Prospectos')
                        ->modalDescription('Estas seguro que deseas eliminar los Prospectos seleccionados? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
                ]),
            ]);
            */
    }

    public static function getRelations(): array
    {
        return [
            NamesRelationManager::class
        ];
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
}
