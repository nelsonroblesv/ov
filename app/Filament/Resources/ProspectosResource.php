<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectosResource\Pages;
use App\Filament\Resources\ProspectosResource\RelationManagers;
use App\Filament\Resources\ProspectosResource\Widgets\ProspectosMapWidget;
use App\Models\Colonias;
use App\Models\Customer;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Paises;
use App\Models\Prospectos;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\table;

class ProspectosResource extends Resource
{
    protected static ?string $model = Prospectos::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationGroup = 'Clientes y Prospectos';
    protected static ?string $navigationLabel = 'Prospectos';
    protected static ?string $breadcrumb = "Prospectos";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Basicos')
                        ->description('Informacion Personal')
                        ->schema([
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

                            TextInput::make('email')
                                ->label('Correo electrónico')
                                ->email()
                                //->required()
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

                            MarkdownEditor::make('notes')
                                ->label('Notas')
                                ->nullable()
                                ->columnSpanFull()
                        ])->columns(2),

                    Step::make('Ubicacion')
                        ->description('Informacion del establecimiento')
                        ->schema([
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

                                ->defaultLocation(fn($record) => [
                                    $record->latitude ?? 20.1845751,
                                    $record->longitude ?? -90.1334567,
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
            //
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
