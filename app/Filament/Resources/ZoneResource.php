<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ZoneResource\Pages;
use App\Filament\Resources\ZoneResource\RelationManagers;
use App\Models\Colonias;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Paises;
use App\Models\Zone;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ZoneResource extends Resource
{
    protected static ?string $model = Zone::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Zonas';
    protected static ?string $breadcrumb = "Zonas";
    //  protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    TextInput::make('name')
                        ->label('Nombre de la Zona')
                        ->helperText('Escribe un nombre unico')
                        ->required()
                        ->maxLength(255),

                    ColorPicker::make('color'),

                    Select::make('paises_id')
                        ->label('País')
                        ->options(Paises::pluck('nombre', 'id'))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            $set('estados_id', null);
                            $set('municipios_id', null);
                            $set('codigo_postal', null);
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
                            $set('codigo_postal', null);
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
                            $set('codigo_postal', null);
                        }),

                    Select::make('codigo_postal')
                        ->label('Código Postal')
                        ->multiple() // Habilitar selección múltiple
                        ->options(function ($get) {
                            return Colonias::where('municipios_id', $get('municipios_id'))
                                ->pluck('codigo_postal', 'codigo_postal'); // Mostrar valores únicos
                        })
                        ->required()
                        ->reactive()
                        ->searchable()
                        ->disabled(fn($get) => !$get('municipios_id'))

                ])->columns(2),

                /*
                Repeater::make('zoneLocations')
                    ->label('Codigo Postal')
                    ->relationship()
                    ->schema([
                        Select::make('codigo_postal')
                            ->label('Codigo Postal')
                            ->options(function ($get) {
                                return Colonias::where('municipios_id', $get('../../municipios_id'))
                                ->select('codigo_postal')
                                ->distinct()    
                                ->pluck('codigo_postal', 'codigo_postal');
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->disabled(function ($get) {
                                return !$get('../../municipios_id');
                            }),
                    ])
                    ->createItemButtonLabel('Agregar Codigo Postal')
                    ->columnSpanFull(),
             */
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Zonas')
            ->description('Gestion de Zonas del sistema.')
            ->columns([
                ColorColumn::make('color')->label('Color'),
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('paises.nombre')->label('Pais'),
                TextColumn::make('estados.nombre')->label('Estado'),
                TextColumn::make('municipios.nombre')->label('Municipio'),
                TextColumn::make('codigo_postal')->label('Codigo Postal'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Zona eliminada')
                                ->body('La Zona ha sido eliminado del sistema.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('danger')
                                ->color('danger')
                        )
                        ->modalHeading('Borrar Zona')
                        ->modalDescription('Estas seguro que deseas eliminar esta Zona? Esta acción no se puede deshacer.')
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
                        ->modalHeading('Borrar Zonas')
                        ->modalDescription('Estas seguro que deseas eliminar las Zonas seleccionados? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
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
            'index' => Pages\ListZones::route('/'),
            'create' => Pages\CreateZone::route('/create'),
            'edit' => Pages\EditZone::route('/{record}/edit'),
            'view' => Pages\ViewZone::route('/{record}'),
        ];
    }
}
