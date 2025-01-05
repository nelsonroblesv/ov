<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Forms\Components\Section;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Expr\Cast\String_;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'Categorias';
    protected static ?string $navigationLabel = 'Familias';
    protected static ?string $breadcrumb = "Familias";
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Información General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Nombre')
                                    ->helperText('Ingresa un nombre para la Familia')
                                    ->disabledOn('edit')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    })
                                    ->suffixIcon('heroicon-m-rectangle-stack'),

                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(Category::class, 'slug', ignoreRecord: true)
                                    ->helperText('Este campo no es editable.')
                                    ->suffixIcon('heroicon-m-at-symbol'),

                                MarkdownEditor::make('description')
                                    ->columnSpan('full')
                                    ->label('Descripción')
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'heading',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ]),
                            ])->columnSpanFull()
                    ]),
                Group::make()
                    ->schema([
                      Section::make('Identificadores')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('url')
                                    ->label('Ingresa una URL')
                                    ->url()
                                    ->suffixIcon('heroicon-m-globe-alt'),

                                    ColorPicker::make('primary_color')
                                    ->label('Selecciona un color')
                                    ->required(),

                                    FileUpload::make('thumbnail')
                                    ->label('Imagen de la Familia')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('category-images')
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->heading('Familias')
        ->description('Familias de la marca.')
            ->columns([
                TextColumn::make('name')->label('Familia')->searchable()->sortable(),
                TextColumn::make('slug')->searchable()->sortable()->label('Slug')->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('thumbnail')->label('Logo'),
                ColorColumn::make('primary_color')->label('Color'),
                ToggleColumn::make('is_active')->label('¿Activo?'),
                TextColumn::make('url')->searchable()->sortable()->label('URL')
            ])
            ->filters([
                //
            ])
            ->actions([
               ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Familia eliminada')
                            ->body('La Familia ha sido eliminada del sistema.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Familia')
                    ->modalDescription('Estas seguro que deseas eliminar esta Familia? Esta acción no se puede deshacer.')
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
                    ->modalHeading('Borrar Familias')
                    ->modalDescription('Estas seguro que deseas eliminar las Familias seleccionadas? Esta acción no se puede deshacer.')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
            'view' => Pages\ViewCategory::route('/{record}'),
        ];
    }
}
