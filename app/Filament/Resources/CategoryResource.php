<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Expr\Cast\String_;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Categorías';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Básicos')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('Nombre')
                                    ->helperText('Ingresa un nombre para la Categoría')
                                    ->disabledOn('edit')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function(string $operation, $state, Forms\Set $set){
                                        if($operation !== 'create'){
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(Category::class, 'slug', ignoreRecord:true),
                                Forms\Components\MarkdownEditor::make('description')
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
                                    ])
                            ])->columns(1)
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Identificadores')
                            ->schema([
                                Forms\Components\TextInput::make('url')
                                    ->label('Ingresa una URL')
                                    ->url(),
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Selecciona un color'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('¿Categoría Activa?')
                                    ->onIcon('heroicon-m-user-plus')
                                    ->offIcon('heroicon-m-user-minus')
                                    ->onColor('success')
                                    ->offColor('danger'),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Imagen de la Categoría')
                                    ->image()
                                    ->imageEditor()
                            ])
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->label('Slug'),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Logo'),
                Tables\Columns\ColorColumn::make('primary_color')
                    ->label('Color'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('¿Activo?'),
                    Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->sortable()
                    ->label('URL'),
                /*Tables\Columns\TextColumn::make('description')
                    ->label('Descripción'),*/
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
