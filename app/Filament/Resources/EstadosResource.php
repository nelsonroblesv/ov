<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadosResource\Pages;
use App\Filament\Resources\EstadosResource\RelationManagers;
use App\Models\Estados;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EstadosResource extends Resource
{
    protected static ?string $model = Estados::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Ubicaciones';
    protected static ?string $navigationLabel = 'Estados';
    protected static ?string $breadcrumb = "Estados";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Select::make('paises_id')
                    ->relationship('paises', 'id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),

                TextColumn::make('paises.nombre')
                    ->label('País')
                    ->sortable(),

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
                            ->title('Estado eliminado')
                            ->body('El Estado ha sido eliminado  del sistema.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Estado')
                    ->modalDescription('Estas seguro que deseas eliminar este Estado? Esta acción no se puede deshacer.')
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
                    ->modalHeading('Borrar Estados')
                    ->modalDescription('Estas seguro que deseas eliminar los Estados seleccionados? Esta acción no se puede deshacer.')
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
            'index' => Pages\ListEstados::route('/'),
            'create' => Pages\CreateEstados::route('/create'),
            'edit' => Pages\EditEstados::route('/{record}/edit'),
        ];
    }
}
