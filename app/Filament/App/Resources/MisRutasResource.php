<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MisRutasResource\Pages;
use App\Filament\App\Resources\MisRutasResource\RelationManagers;
use App\Models\GestionRutas;
use App\Models\MisRutas;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MisRutasResource extends Resource
{
    protected static ?string $model = GestionRutas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Mis Rutas')
            ->description('Estas son tus Rutas.')
            ->reorderable('orden')
            ->columns([
                TextColumn::make('orden')->label('Orden')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('tipo_semana')->label('Dia')->searchable()->sortable(),
                TextColumn::make('dia_semana')->label('Semana')->searchable()->sortable(),
            ])
            ->filters([
                SelectFilter::make('tipo_semana')
                    ->label('Tipo Semana')
                    ->options([
                        'PAR' => 'PAR',
                        'NON' => 'NON',
                    ])
                    ->searchable(),

                SelectFilter::make('dia_semana')
                    ->label('Día de la semana')
                    ->options([
                        'Lun' => 'Lunes',
                        'Mar' => 'Martes',
                        'Mie' => 'Miércoles',
                        'Jue' => 'Jueves',
                        'Vie' => 'Viernes',
                    ]),

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
            'index' => Pages\ListMisRutas::route('/'),
            'create' => Pages\CreateMisRutas::route('/create'),
            'edit' => Pages\EditMisRutas::route('/{record}/edit'),
        ];
    }
}
