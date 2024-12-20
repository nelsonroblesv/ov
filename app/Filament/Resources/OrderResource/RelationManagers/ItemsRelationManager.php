<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction as ActionsCreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles del Producto')
                    ->schema([
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
                            $set('total_price', Product::find($state)?->price_publico ?? 0)),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->dehydrated()
                            ->reactive()
                            ->required()
                            ->debounce(600)
                            //->live(onBlur: true)
                            ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_price', round($state * $get('price_publico'), 2))),

                        TextInput::make('price_publico')
                            ->label('Precio unitario')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->inputMode('decimal')
                            ->required()
                            ->extraInputAttributes(['style' => 'text-align:right']),

                        TextInput::make('total_price')
                            ->label('Precio total')
                            ->numeric()
                            ->inputMode('decimal')
                            ->disabled()
                            ->dehydrated()
                            ->prefixIcon('heroicon-m-currency-dollar')
                            ->prefixIconColor('success')
                            ->extraInputAttributes(['style' => 'text-align:right']), 

                        Placeholder::make('grand_total')
                            ->label('Total a pagar')
                            ->content(function (Get $get, Set $set) {
                                $total = 0;

                                $total = intval($get('quantity')) * $get('price_publico');

                                $set('grand_total', $total);
                                return  Number::currency($total, 'MXN');
                            })
                            ->extraAttributes(['style' => 'text-align:center'])
                            ->columnSpanFull(),

                        Hidden::make('grand_total')
                            ->default(0),

                    ])->columns(3),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading('Productos en el Pedido')
            ->columns([
                Tables\Columns\TextColumn::make('order.number')->label('Orden No.'),
                Tables\Columns\TextColumn::make('product.name')->label('Producto'),
                Tables\Columns\TextColumn::make('quantity')->label('Cantidad'),
                Tables\Columns\TextColumn::make('price_publico')->label('Precio'),
                Tables\Columns\TextColumn::make('total_price')->label('Total')
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
               // ->createAnother(false)
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
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Producto actualizado')
                            ->body('El producto ha sido actualizado.')
                            ->icon('heroicon-o-check')
                            ->iconColor('success')
                            ->color('success')
                    )
                    ->modalHeading('Editar Producto')
                    ->modalDescription('Puedes editar los detalles del producto en el Pedido'),

                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Producto eliminado')
                            ->body('El producto ha sido eliminado del Pedido.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Producto')
                    ->modalDescription('Estas seguro que deseas eliminar este producto del Pedido? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Si, eliminar')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
