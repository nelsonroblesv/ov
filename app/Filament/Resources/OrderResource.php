<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Enums\OrderStatusEnum;
use App\Models\Product;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\Relationship;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Ordenes';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Detalles de la orden')
                        ->schema([
                            Forms\Components\Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                                Forms\Components\TextInput::make('number')
                                ->required()
                                ->disabled()
                                ->default('OR-'.random_int(100000, 9999999))
                                ->dehydrated()
                                ->maxLength(255),
                           
                                Forms\Components\Select::make('status')
                                ->required()
                                ->options([
                                    'pending' => OrderStatusEnum::PENDING->value,
                                    'completed' => OrderStatusEnum::COMPLETED->value,
                                    'processing' => OrderStatusEnum::PROCESSING->value,
                                    'declined' => OrderStatusEnum::DECLINED->value
                                ])
                                ->default('pending')
                                ->columnSpanFull(),
                           
                                Forms\Components\MarkdownEditor::make('notes')
                                ->required()
                                ->columnSpanFull()
                        ])->columns(2),

                    Step::make('Productos')
                        ->schema([
                           Repeater::make('items')
                           ->relationship()
                            ->schema([
                                Select::make('product_id')
                                ->label('Productos')
                                ->searchable()
                                ->options(Product::query()->pluck('name', 'id'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Set $set) => 
                                                    $set('unit_price', Product::find($state)?->price ?? 0)),

                            TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->live()
                                ->dehydrated()
                                ->required(),
                            
                                TextInput::make('unit_price')
                                    ->label('Precio unitario')
                                    ->disabled()
                                    ->dehydrated()
                                    ->numeric()
                                    ->required(),

                                Placeholder::make('total_price')
                                    ->label('Precio total')
                                    ->content(function ($get){
                                        return $get('quantity') * $get('unit_price');
                                    })
                            ])->columns(4)
                        ])
                ])->columnSpanFull()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->colors([
                        'primary',
                        'secondary' => 'pending',
                        'warning' => 'processing',
                        'success' => 'completed',
                        'danger' => 'declined',
                    ])
                    ->icons([
                        'heroicon-o-x',
                        'heroicon-o-exclamation-circle' => 'pending',
                        'heroicon-o-x-mark' => 'declined',
                        'heroicon-o-check' => 'completed',
                        'heroicon-o-clock' => 'processing'
                    ]),
                Tables\Columns\TextColumn::make('notes'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
