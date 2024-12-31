<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaisesResource\Pages;
use App\Filament\Resources\PaisesResource\RelationManagers;
use App\Models\Paises;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaisesResource extends Resource
{
    protected static ?string $model = Paises::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Ubicaciones';
    protected static ?string $navigationLabel = 'Paises';
    protected static ?string $breadcrumb = "Paises";
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
                            ->title('Pais eliminado')
                            ->body('El Pais ha sido eliminado  del sistema.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Pais')
                    ->modalDescription('Estas seguro que deseas eliminar este Pais? Esta acción no se puede deshacer.')
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
                    ->modalHeading('Borrar Paises')
                    ->modalDescription('Estas seguro que deseas eliminar los Paises seleccionados? Esta acción no se puede deshacer.')
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
            'index' => Pages\ListPaises::route('/'),
            'create' => Pages\CreatePaises::route('/create'),
            'edit' => Pages\EditPaises::route('/{record}/edit'),
        ];
    }
}
