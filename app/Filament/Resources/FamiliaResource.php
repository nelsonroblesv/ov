<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamiliaResource\Pages;
use App\Filament\Resources\FamiliaResource\RelationManagers;
use App\Models\Category;
use App\Models\Familia;
use App\Models\Marca;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class FamiliaResource extends Resource
{
    protected static ?string $model = Familia::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';
    protected static ?string $navigationGroup = 'Catalogo';
    protected static ?string $navigationLabel = 'Familias';
    protected static ?string $breadcrumb = "Familias";
    protected static ?int $navigationSort = 3;

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

                                Select::make('marcas_id')
                                    ->required()
                                    ->label('Marca')
                                    ->options(Marca::pluck('name', 'id')),

                                Select::make('categories')
                                    ->required()
                                    ->label('Categoria')
                                    ->multiple()
                                    ->preload()
                                    ->options(Category::pluck('name', 'name')),

                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(Familia::class, 'slug', ignoreRecord: true)
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
                ImageColumn::make('thumbnail')->label('Logo'),
                ColorColumn::make('primary_color')->label('Color'),
                TextColumn::make('name')->label('Familia')->searchable()->sortable(),
                TextColumn::make('marcas.name')->label('Marca')->searchable()->sortable(),
                TextColumn::make('categories')->label('Categorias')->searchable()->sortable(),
                TextColumn::make('slug')->searchable()->sortable()->label('Slug')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('url')->searchable()->sortable()->label('URL'),
                ToggleColumn::make('is_active')->label('¿Activo?')->toggleable(isToggledHiddenByDefault: true)
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
            'index' => Pages\ListFamilias::route('/'),
            'create' => Pages\CreateFamilia::route('/create'),
            'edit' => Pages\EditFamilia::route('/{record}/edit'),
            'view' => Pages\ViewFamilia::route('/{record}'),
        ];
    }
}
