<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ZonasResource\Pages;
use App\Filament\Resources\ZonasResource\RelationManagers;
use App\Models\Regiones;
use App\Models\Zonas;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ZonasResource extends Resource
{
    protected static ?string $model = Zonas::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Ajustes';
    protected static ?string $navigationLabel = 'Zonas';
    protected static ?string $breadcrumb = "Zonas";
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Zonas')->schema([
                    Select::make('regiones_id')
                        ->label('Región')
                        ->placeholder('Seleccione una región')
                        ->options(
                            Regiones::all()->pluck('name', 'id')
                        )
                        ->required()
                        ->preload(),
                ]),

                Section::make('')->schema([
                    TextInput::make('nombre_zona')
                        ->label('Nombre')
                        ->placeholder('Nombre de la zona')
                        ->required(),

                    ColorPicker::make('color_zona')
                        ->label('Color')
                        ->placeholder('Color de la zona')
                        ->required(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_zona') ->label('Nombre')->searchable()->sortable(),
                ColorColumn::make('color_zona') ->label('Color')->searchable()->sortable(),
                TextColumn::make('regiones.name') ->label('Región')->searchable()->sortable(),
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
            'index' => Pages\ListZonas::route('/'),
            'create' => Pages\CreateZonas::route('/create'),
            'edit' => Pages\EditZonas::route('/{record}/edit'),
        ];
    }
}
