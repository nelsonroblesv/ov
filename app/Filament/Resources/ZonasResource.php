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
                        ->required(),
                ]),

                Repeater::make('zonas')
                    ->label('Zonas')
                    ->schema([
                        Section::make('')->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->placeholder('Nombre de la zona')
                                ->required()
                                ->columns(),

                            ColorPicker::make('color')
                                ->label('Color')
                                ->placeholder('Color de la zona')
                                ->required(),
                        ])->columns(2)
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
