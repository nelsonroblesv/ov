<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignarTipoSemanaResource\Pages;
use App\Filament\Resources\AsignarTipoSemanaResource\RelationManagers;
use App\Models\AsignarTipoSemana;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsignarTipoSemanaResource extends Resource
{
    protected static ?string $model = AsignarTipoSemana::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tipo_semana')
                    ->options([
                        '0' => 'PAR',
                        '1' => 'NON',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('tipo_semana')
                    ->label('Semana Par/Non'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
               
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
            'index' => Pages\ListAsignarTipoSemanas::route('/'),
            'create' => Pages\CreateAsignarTipoSemana::route('/create'),
            'edit' => Pages\EditAsignarTipoSemana::route('/{record}/edit'),
        ];
    }
}
