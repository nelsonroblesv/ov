<?php

namespace App\Filament\Resources\PreferredModuleResource\RelationManagers;

use App\Models\Product;
use Illuminate\Support\Number;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreferredItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'preferredItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informacion del producto')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->label('Producto')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, $set) => $set(
                                'price_publico',
                                Product::find($state)?->price_publico ?? 0
                            ))
                            ->afterStateUpdated(fn($state, $set) => $set(
                                'price_salon',
                                Product::find($state)?->price_salon ?? 0
                            )),

                        TextInput::make('quantity')
                            ->numeric()
                            ->label('Cantidad')
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->dehydrated()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set, $get) => $set(
                                'total_price_publico',
                                round($state * $get('price_publico'), 2)
                            ))
                            ->afterStateUpdated(fn($state, $set, $get) => $set(
                                'total_price_salon',
                                round($state * $get('price_salon'), 2)
                            )),

                        TextInput::make('price_publico')
                            ->label('Precio Publico')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('total_price_publico')
                            ->label('Precio Total Publico')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('price_salon')
                            ->label('Precio Salon')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('total_price_salon')
                            ->label('Precio Total Salon')
                            ->disabled()
                            ->dehydrated(),


                    ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Productos del Modulo')
            ->description('Productos que pertenecen a este modulo')
            ->columns([
                TextColumn::make('product.name')->label('Producto'),
                TextColumn::make('quantity')->label('Cantidad'),
                TextColumn::make('total_price_publico')->label('Precio Publico')->summarize(Sum::make()->label('Total')),
                TextColumn::make('total_price_salon')->label('Precio Salon')->summarize(Sum::make()->label('Total'))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
