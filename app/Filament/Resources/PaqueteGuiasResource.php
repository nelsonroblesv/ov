<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaqueteGuiasResource\Pages;
use App\Filament\Resources\PaqueteGuiasResource\RelationManagers;
use App\Models\PaqueteGuias;
use App\Models\Regiones;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaqueteGuiasResource extends Resource
{
    protected static ?string $model = PaqueteGuias::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Logística';
    protected static ?string $navigationLabel = 'Paquete de Guias';
    protected static ?string $breadcrumb = "Paquete de Guías";
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Section::make('Informacion del Paquete')->schema([

                Hidden::make('user_id')->default(fn() => auth()->id()),

                TextInput::make('periodo')
                ->required(),

                Select::make('semana')
                ->label('Semana')
                ->options([
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ]),

                Select::make('regiones_id')
                ->label('Región')
                ->placeholder('Seleccione una región')
                ->required()
                ->options(
                    Regiones::query()
                        ->where('is_active', true)
                        ->pluck('name', 'id')
                ),

                Select::make('estado')
                    ->label('Estado del Paquete')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'completado' => 'Completado',
                    ])->default('pendiente')

               ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('semana')
                    ->label('Semana'),
                TextColumn::make('periodo')
                    ->label('Periodo'),
                TextColumn::make('regiones.name')
                    ->label('Región'),
                TextColumn::make('users.name')
                    ->label('Registrado por'),
                TextColumn::make('created_at')
                    ->label('Fecha de Registro')
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
            'index' => Pages\ListPaqueteGuias::route('/'),
            'create' => Pages\CreatePaqueteGuias::route('/create'),
            'edit' => Pages\EditPaqueteGuias::route('/{record}/edit'),
        ];
    }
}
