<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Catalogo';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $breadcrumb = "Productos";
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre')
                            ->helperText('Ingresa el nombre del Producto')
                            ->unique(ignoreRecord:true)
                            //->disabledOn('edit')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->helperText('Este campo no es editable.'),

                        Select::make('marca_id')
                            ->required()
                            ->label('Marca')
                            ->relationship('marca', 'name'),

                        Select::make('familia_id')
                            ->required()
                            ->label('Familia')
                            ->relationship('familia', 'name'),

                        FileUpload::make('thumbnail')
                            ->label('Imagen del Producto')
                            ->image()
                            ->imageEditor()
                            ->directory('product-images'),
                        
                            TextInput::make('sku')
                    ])->columns(2),

                Section::make('Inventario')
                    ->schema([

                        TextInput::make('price_publico')
                            ->label('Precio Publico')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->reactive()
                            ->dehydrated()
                            //->debounce(600)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $price_salon = $state * 1.7;
                                $price_distribuidor = $state * 2.4;

                                $set('price_salon', round($price_salon, 2));
                                $set('price_distribuidor', round($price_distribuidor, 2));
                            }),

                        TextInput::make('price_distribuidor')
                            ->label('Precio Distribuidor')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),

                        TextInput::make('price_salon')
                            ->label('Precio Salon')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),

                        /*TextInput::make('sku')
                            //->required()
                            ->label('SKU')
                            ->maxLength(255),*/
                    ])->columns(2),

                Section::make('Extras')
                    ->schema([
                        MarkdownEditor::make('description')
                            ->label('Descripción e información adicional')
                            ->columnSpanFull()
                    ]),

                Section::make('Control')
                    ->schema([
                        Toggle::make('visibility')
                            ->label('Visible')
                            ->default(true)
                            ->onIcon('heroicon-m-eye')
                            ->offIcon('heroicon-m-eye-slash')
                            ->onColor('success')
                            ->offColor('danger'),

                        Toggle::make('availability')
                            ->label('Disponible')
                            ->default(true)
                            ->onIcon('heroicon-m-check-circle')
                            ->offIcon('heroicon-m-no-symbol')
                            ->onColor('success')
                            ->offColor('danger'),

                        Toggle::make('shipping')
                            ->label('Para Envío')
                            ->default(true)
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
            ->heading('Productos')
            ->description('Gestion de Productos de la marca.')
            ->columns([
                ImageColumn::make('thumbnail')->label('Imagen'),
                TextColumn::make('name')->label('Producto')->searchable()->sortable(),
                TextColumn::make('marca.name')->label('Marca')->searchable()->sortable(),
                TextColumn::make('familia.name')->label('Familia')->searchable()->sortable(),
                TextColumn::make('sku')->label('SKU')->searchable()->toggleable(isToggledHiddenByDefault: true),
               // TextColumn::make('price_distribuidor')->label('Distribuidor')->sortable(),
                TextColumn::make('price_salon')->label('Salon')->sortable(),
                TextColumn::make('price_publico')->label('Publico')->sortable(),
                IconColumn::make('visibility')->label('Visible')->boolean()
                    ->trueIcon('heroicon-o-eye')->falseIcon('heroicon-o-eye-slash')->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('availability')->label('Disponible')->boolean()
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-no-symbol')->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('shipping')->label('Envío')->boolean()
                    ->trueIcon('heroicon-o-truck')->falseIcon('heroicon-o-no-symbol')->toggleable(isToggledHiddenByDefault: true),
                //TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                //TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
                                ->title('Producto eliminado')
                                ->body('El Producto ha sido eliminado  del sistema.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('danger')
                                ->color('danger')
                        )
                        ->modalHeading('Borrar Producto')
                        ->modalDescription('Estas seguro que deseas eliminar este Producto? Esta acción no se puede deshacer.')
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
                        ->modalHeading('Borrar Productos')
                        ->modalDescription('Estas seguro que deseas eliminar los Productos seleccionados? Esta acción no se puede deshacer.')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'Administrador';
    }
}
