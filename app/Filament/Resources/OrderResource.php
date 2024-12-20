<?php

namespace App\Filament\Resources;

use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

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
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\OrderItem;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $breadcrumb = "Pedidos";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Detalles del Pedido')
                        ->schema([
                            Forms\Components\Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->disabledOn('edit')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\TextInput::make('number')
                                ->required()
                                ->disabled()
                                ->default('OR-' . random_int(100000, 9999999))
                                ->dehydrated()
                                ->maxLength(255),

                            Forms\Components\ToggleButtons::make('status')
                                ->required()
                                ->inline()
                                ->options([
                                    'pending' => OrderStatusEnum::PENDING->value,
                                    'completed' => OrderStatusEnum::COMPLETED->value,
                                    'processing' => OrderStatusEnum::PROCESSING->value,
                                    'declined' => OrderStatusEnum::DECLINED->value
                                ])
                                ->colors([
                                    'pending' => 'info',
                                    'processing' => 'warning',
                                    'completed' => 'success',
                                    'declined' => 'danger',
                                ])
                                ->icons([
                                    'pending' => 'heroicon-o-exclamation-circle',
                                    'processing' => 'heroicon-o-arrow-path',
                                    'completed' => 'heroicon-o-check',
                                    'declined' => 'heroicon-o-x-mark'
                                ])
                                ->default('pending')
                                ->columnSpanFull(),
                        ])->columns(2),

                    Step::make('Informacion del Pedido')
                        ->schema([
                            MarkdownEditor::make('notes')
                                ->columnSpanFull()
                        ])
                ])->columnSpanFull()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Pedidos')
            ->description('Gestion de Pedidos.')
            ->columns([
                TextColumn::make('number')
                    ->label('Num. Orden')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->colors([
                        'primary',
                        'info' => 'pending',
                        'warning' => 'processing',
                        'success' => 'completed',
                        'danger' => 'declined',
                    ])
                    ->icons([
                        'heroicon-o-x',
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-x-mark' => 'declined',
                        'heroicon-o-check' => 'completed',
                        'heroicon-o-arrow-path' => 'processing'
                    ]),
                TextColumn::make('notes')
                    ->label('Notas')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('grand_total')
                    ->label('Total'),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
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
                    Tables\Actions\DeleteAction::make(),
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
            ItemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', '-', 'pending')->count() > 1 ? 'success' : 'info';
    }

    protected static ?string $navigationBadgeTooltip = 'Ordenes Pendientes';
}
