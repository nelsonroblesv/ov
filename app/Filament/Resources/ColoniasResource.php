<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColoniasResource\Pages;
use App\Filament\Resources\ColoniasResource\RelationManagers;
use App\Models\Colonias;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ColoniasResource extends Resource
{
    protected static ?string $model = Colonias::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Ubicaciones';
    protected static ?string $navigationLabel = 'Colonias';
    protected static ?string $breadcrumb = "Colonias";
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ciudad')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('municipios_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('asentamiento')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('codigo_postal')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),

                TextColumn::make('ciudad')
                    ->searchable(),
                    
                TextColumn::make('municipios.nombre')
                    ->label('Municipio')
                    ->sortable(),

                TextColumn::make('asentamiento')
                    ->searchable(),

                TextColumn::make('codigo_postal')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Colonia eliminada')
                            ->body('La Colonia ha sido eliminado  del sistema.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Colonia')
                    ->modalDescription('Estas seguro que deseas eliminar esta Colonia? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Si, eliminar'),
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
                    ->modalHeading('Borrar Colonias')
                    ->modalDescription('Estas seguro que deseas eliminar las Colonias seleccionados? Esta acción no se puede deshacer.')
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
            'index' => Pages\ListColonias::route('/'),
            'create' => Pages\CreateColonias::route('/create'),
            'edit' => Pages\EditColonias::route('/{record}/edit'),
        ];
    }
}
