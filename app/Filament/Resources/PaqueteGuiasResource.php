<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuiaRelationManagerResource\RelationManagers\GuiaRelationManager;
use App\Filament\Resources\PaqueteGuiasResource\Pages;
use App\Filament\Resources\PaqueteGuiasResource\RelationManagers;
use App\Filament\Resources\PaqueteGuiasResource\RelationManagers\GuiasRelationManager;
use App\Models\PaqueteGuias;
use App\Models\Regiones;
use Carbon\Carbon;
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
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    Select::make('semana')
                        ->label('Semana')
                        ->options([
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                        ]),

                    TextInput::make('num_semana')
                        ->label('Semana del año')
                        ->default(fn() => Carbon::now()->isoWeek())   // ← se llena con la semana actual
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(53)
                        ->required(),

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
                            'rev' => 'En Revisión',
                            'fal' => 'Con Faltantes',
                            'com' => 'Completado',
                        ])->default('pendiente')

                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('periodo')
                    ->label('Periodo')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('semana')
                    ->label('Semana')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('num_semana')
                    ->label('# Semana')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('regiones.name')
                    ->label('Región'),
                TextColumn::make('estado')
                    ->label('Estado del Paquete')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'rev' => 'En Revisión',
                        'fal' => 'Con Faltantes',
                        'com' => 'Completado',
                    ][$state] ?? 'Otro')
                    ->colors([
                        'warning' => 'rev',
                        'danger' => 'fal',
                        'success' => 'com',
                    ])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('users.name')
                    ->label('Registrado por'),

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
<<<<<<< HEAD
<<<<<<< HEAD
           
=======
           GuiasRelationManager::class
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
=======
           GuiasRelationManager::class
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
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
