<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\PreferredModuleResource\Pages;
use App\Filament\Resources\PreferredModuleResource\RelationManagers;
use App\Filament\Resources\PreferredModuleResource\RelationManagers\ItemsRelationManager as RelationManagersItemsRelationManager;
use App\Filament\Resources\PreferredModuleResource\RelationManagers\PreferredItemsRelationManager;
use App\Models\PreferredModule;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreferredModuleResource extends Resource
{
    protected static ?string $model = PreferredModule::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Catalogo';
    protected static ?string $navigationLabel = 'Modulos Preferred';
    protected static ?string $breadcrumb = "Modulos Preferred";
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del módulo')
                    ->schema([
                        TextInput::make('module_name')
                            ->label('Nombre')
                            ->unique()
                            ->required()
                            ->disabledOn('edit')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('preferredItems.quantity')
                    ->label('Cantidad de Productos'),

                TextColumn::make('grand_total')
                    ->label('Costo')


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    //Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Modulo eliminado')
                            ->body('El Modulo ha sido eliminado del sistema.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Modulo')
                    ->modalDescription('Estas seguro que deseas eliminar este Modulo? Esta acción no se puede deshacer.')
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
                    ->modalHeading('Borrar Modulos')
                    ->modalDescription('Estas seguro que deseas eliminar los Modulos seleccionados? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Si, eliminar'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PreferredItemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPreferredModules::route('/'),
            'create' => Pages\CreatePreferredModule::route('/create'),
            'edit' => Pages\EditPreferredModule::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'Administrador';
    }
}
