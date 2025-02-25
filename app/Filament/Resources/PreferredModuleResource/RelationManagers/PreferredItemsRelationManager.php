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
use Filament\Notifications\Notification;
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
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, $set) => $set(
                                'price_publico',
                                Product::find($state)?->price_publico ?? 0
                            ))
                            ->afterStateUpdated(fn($state, $set) => $set(
                                'price_salon',
                                Product::find($state)?->price_salon ?? 0
                            ))
                            ->afterStateUpdated(fn($state, $set, $get) => $set(
                                'total_price_publico',
                                round($state * $get('price_publico'), 2)
                            ))
                            ->afterStateUpdated(fn($state, $set, $get) => $set(
                                'total_price_salon',
                                round($state * $get('price_salon'), 2)
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
                            ->reactive()
                            ->dehydrated(),

                        TextInput::make('total_price_publico')
                            ->label('Precio Total Publico')
                            ->disabled()
                            ->reactive()
                            ->dehydrated(),

                        TextInput::make('price_salon')
                            ->label('Precio Salon')
                            ->reactive()
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('total_price_salon')
                            ->label('Precio Total Salon')
                            ->reactive()
                            ->disabled()
                            ->dehydrated()

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
                Tables\Actions\CreateAction::make()
                ->label('Agregar productos')
                ->icon('heroicon-o-shopping-cart')
                ->modalHeading('Agregar producto al Modulo')
                ->modalSubmitActionLabel('Agregar')
               // ->createAnother(false)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Producto agregado')
                        ->body('Puedes seguir agregando productos al Modulo.')
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
                            ->title('Modulo actualizado')
                            ->body('El Modulo ha sido actualizado.')
                            ->icon('heroicon-o-check')
                            ->iconColor('success')
                            ->color('success')
                    )
                    ->modalHeading('Editar Producto')
                    ->modalDescription('Puedes editar los detalles del producto en el Modulo'),

                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Modulo eliminado')
                            ->body('El producto ha sido eliminado del Modulo.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Producto')
                    ->modalDescription('Estas seguro que deseas eliminar este producto del Modulo? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Si, eliminar')
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
                    ->modalDescription('Estas seguro que deseas eliminar los Productos seleccionados del Modulo? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Si, eliminar'),
                ]),
            ]);
    }
}