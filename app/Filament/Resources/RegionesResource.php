<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionesResource\Pages;
use App\Filament\Resources\RegionesResource\RelationManagers;
use App\Models\Regiones;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Markdown;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegionesResource extends Resource
{
    protected static ?string $model = Regiones::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Ajustes';
    protected static ?string $navigationLabel = 'Regiones';
    protected static ?string $breadcrumb = "Regiones";
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Regiones')->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->placeholder('Nombre de la región')
                        ->required(),

                    MarkdownEditor::make('description')
                        ->label('Descripción')
                        ->placeholder('Descripción de la región')

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Regiones')
            ->description('Gestion de las Regiones de Prospeccion y Clientes.')
            ->columns([
                TextColumn::make('name')->label('Nombre de la Region'),
                TextColumn::make('description')->label('Descripcion')
                ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Región eliminada')
                                ->body('La Región ha sido eliminada  del sistema.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('danger')
                                ->color('danger')
                        )
                        ->modalHeading('Borrar Región')
                        ->modalDescription('Estas seguro que deseas eliminar esta Región? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
                ])
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
                        ->modalHeading('Borrar Regiones')
                        ->modalDescription('Estas seguro que deseas eliminar Regiones seleccionadas? Esta acción no se puede deshacer.')
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
            'index' => Pages\ListRegiones::route('/'),
            'create' => Pages\CreateRegiones::route('/create'),
            'edit' => Pages\EditRegiones::route('/{record}/edit'),
        ];
    }
}
