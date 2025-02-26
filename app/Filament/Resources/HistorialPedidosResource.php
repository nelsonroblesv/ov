<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\HistorialPedidosResource\Pages;
use App\Filament\Resources\HistorialPedidosResource\RelationManagers;
use App\Models\HistorialPedidos;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Tables\Actions\ActionGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistorialPedidosResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Historial de Pedidos';
    protected static ?string $breadcrumb = "Historial de Pedidos";
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles del Pedido')->schema([

                    TextInput::make('number')
                        ->label('Numero de Orden')
                        ->required()
                        ->disabled()
                        ->default('OR-' . random_int(100000, 9999999))
                        ->dehydrated()
                        ->maxLength(255),

                    Select::make('customer_id')
                        ->label('Cliente')
                        ->relationship('customer', 'name')
                        ->disabledOn('edit')
                        ->searchable()
                        ->preload()
                        ->required(),

                    ToggleButtons::make('status')
                        ->label('Estado del Pedido')
                        ->required()
                        ->inline()
                        ->options([
                            'pending' => OrderStatusEnum::PENDING->value,
                            'completed' => OrderStatusEnum::COMPLETED->value,
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
                        ->default('completed'),

                    TextInput::make('notes')
                        ->label('Notas')
                        ->nullable(),
                    
                    DatePicker::make('created_at')
                        ->label('Fecha')
                        ->required()
                        ->native(),

                    TextInput::make('grand_total')
                        ->label('Total')
                        ->required()
                        ->numeric()
                        ->default(0)
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Pedidos')
            ->description('Historial de Pedidos.')
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
                    ->label('Total')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
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
            'index' => Pages\ListHistorialPedidos::route('/'),
            'create' => Pages\CreateHistorialPedidos::route('/create'),
            'edit' => Pages\EditHistorialPedidos::route('/{record}/edit'),
        ];
    }
}
