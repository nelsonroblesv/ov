<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventosResource\Pages;
use App\Filament\Resources\EventosResource\RelationManagers;
use App\Models\Eventos;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
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

class EventosResource extends Resource
{
    protected static ?string $model = Eventos::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'OValle';
    protected static ?string $navigationLabel = 'Eventos';
    protected static ?string $breadcrumb = "Eventos";
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles')->schema([
                    Select::make('tipo')->label('Tipo de evento')
                        ->options([
                            'Webinar' => 'Webinar',
                            'Workshop' => 'Workshop',
                            'Conferencia' => 'Conferencia'
                        ]),
                    TextInput::make('nombre')->label('Nombre del evento'),
                    ColorPicker::make('color')->label('Color del evento'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo'),
                TextColumn::make('nombre'),
                ColorColumn::make('color')
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
            'index' => Pages\ListEventos::route('/'),
            'create' => Pages\CreateEventos::route('/create'),
            'edit' => Pages\EditEventos::route('/{record}/edit'),
        ];
    }
}
