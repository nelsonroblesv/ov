<?php

namespace App\Filament\Resources\PedidosResource\RelationManagers;

use App\Models\PedidosItems;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('product_id')
                        ->relationship('product', 'name')
                        ->label('Productos')
                        ->placeholder('Selecciona un producto')
                        ->preload()
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->distinct()
                        ->columnSpanFull()
                        ->afterStateUpdated(fn($state, Set $set) =>
                        $set('price_publico', Product::find($state)?->price_publico ?? 0))
                        ->afterStateUpdated(fn($state, Set $set) =>
                        $set('total_price', Product::find($state)?->price_publico ?? 0))
                        ->columnSpan(2),

                    Hidden::make('price_publico')
                        ->label('Precio unitario')
                        ->disabled()
                        ->dehydrated(),

                    TextInput::make('quantity')
                        ->label('Cantidad')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->dehydrated()
                        ->reactive()
                        ->required()
                        ->debounce(600)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_price', round($state * $get('price_publico'), 2)))
                        ->columnSpan(1),

                    Hidden::make('total_price')
                        ->dehydrated(),
                ])->columns(3)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->heading('Productos en el Pedido')
            ->emptyStateHeading('No se agregaron productos al Pedido.')
            ->emptyStateDescription('Agrega productos al pedido para visualizarlos aquí.')
            ->columns([
                //TextColumn::make('pedido.id_nota')->label('Orden No.'),
                TextColumn::make('product.name')->label('Producto'),
                TextColumn::make('quantity')->label('Cantidad'),
                TextColumn::make('product.price_publico')->label('Precio'),
                TextColumn::make('total_price'),
                TextColumn::make('total_price')->label('Subtotal')
                    ->summarize(Sum::make()
                        ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2))
                        ->label('Total del Pedido')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar productos')
                    ->icon('heroicon-o-shopping-cart')
                    ->modalHeading('Agregar producto al Pedido')
                    ->modalSubmitActionLabel('Agregar')
                    ->createAnother(false)
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Producto agregado')
                            ->body('Puedes seguir agregando productos al Pedido.')
                            ->icon('heroicon-o-check')
                            ->iconColor('success')
                            ->color('success')
                    )
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Eliminar producto del Pedido')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este producto del Pedido? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Eliminar')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Producto eliminado')
                            ->body('El producto ha sido eliminado del Pedido.')
                            ->icon('heroicon-o-check')
                            ->iconColor('success')
                            ->color('success')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
