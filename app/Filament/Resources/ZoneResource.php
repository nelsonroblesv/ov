<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ZoneResource\Pages;
use App\Filament\Resources\ZoneResource\RelationManagers;
use App\Models\Municipality;
use App\Models\State;
use App\Models\Zone;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
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
                Section::make('Datos de la Zona')->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required(),

                    ColorPicker::make('color')
                        ->label('Asigna un color')
                        ->required()
                ])->columns(2),

                Repeater::make('ubicaciones')
                    ->relationship('ubicaciones')
                    ->schema([
                        Section::make('')->schema([
                            Select::make('state_id')
                            ->label('Estado')
                            ->options(State::query()->pluck('name', 'id'))
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('municipality_id')
                            ->label('Municipio')
                            ->options(function (callable $get) {
                                $stateId = $get('state_id');

                                if (!$stateId) {
                                    return [];
                                }

                                return Municipality::whereHas('state', function ($query) use ($stateId) {
                                    $query->where('state_id', $stateId);
                                })->pluck('name', 'id');
                            })
                            ->disabled(function (callable $get) {
                                return !$get('state_id');
                            })
                            ->reactive()
                            ->required()
                            ->searchable()
                            ->preload()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')->label('Color'),
                TextColumn::make('name')->label('Nombre'),
              //  TextColumn::make('ubicaciones.state.name')->label('Estado'),
                TextColumn::make('ubicaciones.municipality.name')->label('Municipio')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
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
            'index' => Pages\ListZones::route('/'),
            'create' => Pages\CreateZone::route('/create'),
            'edit' => Pages\EditZone::route('/{record}/edit'),
        ];
    }
}
