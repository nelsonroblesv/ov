<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre')
                            ->helperText('Ingresa el nombre del Producto')
                            ->disabledOn('edit')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->helperText('Este campo no es editable.'),

                        Forms\Components\Select::make('category_id')
                            ->required()
                            ->label('Familia')
                            ->relationship('category', 'name'),

                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Imagen del Producto')
                            ->image()
                            ->imageEditor()
                            ->directory('product-images')
                    ])->columns(2),

                Section::make('Inventario')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->label('Precio'),

                        Forms\Components\TextInput::make('sku')
                            ->required()
                            ->label('SKU')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Extras')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('description')
                            ->label('Descripción e información adicional')
                            ->columnSpanFull()
                    ]),

                Section::make('Control')
                    ->schema([
                        Forms\Components\Toggle::make('visibility')
                            ->label('Visible')
                            ->onIcon('heroicon-m-eye')
                            ->offIcon('heroicon-m-eye-slash')
                            ->onColor('success')
                            ->offColor('danger'),

                        Forms\Components\Toggle::make('availability')
                            ->label('Disponible')
                            ->onIcon('heroicon-m-check-circle')
                            ->offIcon('heroicon-m-no-symbol')
                            ->onColor('success')
                            ->offColor('danger'),

                        Forms\Components\Toggle::make('shipping')
                            ->label('Para Envío')
                            ->onIcon('heroicon-m-truck')
                            ->offIcon('heroicon-m-no-symbol')
                            ->onColor('success')
                            ->offColor('danger')
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Imagen'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Familia')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),

                Tables\Columns\IconColumn::make('visibility')
                    ->label('Visible')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('availability')
                    ->label('Disponible')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('shipping')
                    ->label('Envío')
                    ->boolean()
                    ->trueIcon('heroicon-o-truck')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->toggleable(isToggledHiddenByDefault: true),

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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
