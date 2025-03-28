<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaqueteGuiaResource\Pages;
use App\Filament\Resources\PaqueteGuiaResource\RelationManagers;
use App\Models\PaqueteGuia;
use App\Models\Regiones;
use App\Models\Zonas;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaqueteGuiaResource extends Resource
{
    protected static ?string $model = PaqueteGuia::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Logistica';
    protected static ?string $navigationLabel = 'Paquetes de Guías';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos del Paquete')->schema([
                    TextInput::make('periodo')
                        ->required()
                        ->maxLength(10),

                    TextInput::make('semana')
                        ->required(),

                    Select::make('regiones_id')
                        ->label('Región')
                        //  ->required()
                        ->options(
                            fn() =>
                            Regiones::whereIn('id', function ($query) {
                                $query->select('regiones_id')
                                    ->from('zonas');
                            })->pluck('name', 'id')
                        )
                        ->reactive(),

                    Select::make('zonas_id')
                        ->label('Zona')
                        ->placeholder('Selecciona una zona')
                        // ->required()
                        ->searchable()
                        ->options(
                            fn(callable $get) =>
                            Zonas::where('regiones_id', $get('regiones_id'))
                                ->whereIn('id', function ($query) {
                                    $query->select('id')
                                        ->from('zonas');
                                })
                                ->pluck('nombre_zona', 'id')
                        )

                        ->reactive()
                        ->disabled(fn(callable $get) => empty($get('regiones_id'))),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('periodo')->sortable()->searchable(),
                TextColumn::make('semana')->sortable(),
                TextColumn::make('regiones_id')->label('Región')->sortable()->searchable(),
                TextColumn::make('zonas_id')->label('Zona')->sortable()->searchable(),
                TextColumn::make('estado')->badge()
                    ->colors([
                        'secondary' => 'Pendiente',
                        'success' => 'Completado',
                    ]),
                TextColumn::make('created_at')->dateTime()->label('Registro')
                    ->sortable()
                    ->toggleable(),
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
            'create' => Pages\CreatePaqueteGuia::route('/create'),
            'edit' => Pages\EditPaqueteGuia::route('/{record}/edit'),
        ];
    }
}
