<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\HistorialPedidosResource\Pages;
use App\Models\Customer;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HistorialPedidosResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
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
                        ->maxLength(255)
                        ->suffixIcon('heroicon-m-hashtag'),

                    Select::make('customer_id')
                        ->label('Cliente')
                        ->disabledOn('edit')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->suffixIcon('heroicon-m-user')
                        ->options(Customer::query()
                            ->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
                            ->pluck('name', 'id')),


                    ToggleButtons::make('status')
                        ->label('Estado del Pedido')
                        ->required()
                        ->inline()
                        ->options([
                            'pending' => OrderStatusEnum::PENDING->value,
                            'completed' => OrderStatusEnum::COMPLETED->value,
                            'declined' => OrderStatusEnum::DECLINED->value,
                            'cancelled' => OrderStatusEnum::CANCELLED->value,
                            'partial' => OrderStatusEnum::PARTIAL->value
                        ])
                        ->colors([
                            'pending' => 'info',
                            'processing' => 'warning',
                            'completed' => 'success',
                            'declined' => 'gray',
                            'cancelled' => 'danger',
                            'partial' => 'warning',
                        ])
                        ->icons([
                            'pending' => 'heroicon-o-exclamation-circle',
                            'processing' => 'heroicon-o-arrow-path',
                            'completed' => 'heroicon-o-check',
                            'declined' => 'heroicon-o-x-mark',
                            'cancelled' => 'heroicon-o-x-circle',
                            'partial' => 'heroicon-o-arrow-uturn-left'
                        ])
                        ->default('pending')
                        ->columnSpanFull(),

                    DatePicker::make('created_at')
                        ->label('Fecha')
                        ->required()
                        ->native(),

                    DatePicker::make('fecha_liquidacion')
                        ->label('Fecha de liquidación')
                        ->required()
                        ->native(),

                    TextInput::make('notes')
                        ->label('Notas adicionales del Pedido')
                        ->nullable()
                        ->suffixIcon('heroicon-m-pencil-square'),

                    TextInput::make('grand_total')
                        ->label('Total')
                        ->required()
                        ->numeric()
                        ->placeholder('0.00')
                        ->suffixIcon('heroicon-m-currency-dollar'),

                    FileUpload::make('notas_venta')
                        ->label('Notas de Venta')
                        ->placeholder('Haz click para cargar la(s) nota(s) de venta')
                        ->multiple()
                        ->directory('notas_venta')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull()
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
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

                TextColumn::make('fecha_liquidacion')
                    ->label('Liquidadción')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->colors([
                        'primary',
                        'info' => 'pending',
                        'success' => 'completed',
                        'gray' => 'declined',
                        'danger' => 'cancelled',
                        'warning' => 'partial',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check' => 'completed',
                        'heroicon-o-x-mark' => 'declined',
                        'heroicon-o-x-circle' => 'cancelled',
                        'heroicon-o-arrow-uturn-left' => 'partial',

                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'pending' => 'Pendiente',
                        'completed' => 'Completado',
                        'declined' => 'Rechazado',
                        'cancelled' => 'Cancelado',
                        'partial' => 'Devuelta Parcial',
                    ][$state] ?? 'Otro'),

                TextColumn::make('notes')
                    ->label('Notas')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('grand_total')
                    ->label('Total')
                    ->prefix('$')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Pedido eliminado')
                                ->body('El Pedido ha sido eliminada del sistema.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('danger')
                                ->color('danger')
                        )
                        ->modalHeading('Borrar Pedido')
                        ->modalDescription('Estas seguro que deseas eliminar este Pedido? Esta acción no se puede deshacer.')
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
                        ->modalHeading('Borrar Pedidos')
                        ->modalDescription('Estas seguro que deseas eliminar los Pedidos seleccionadas? Esta acción no se puede deshacer.')
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
            'index' => Pages\ListHistorialPedidos::route('/'),
            'create' => Pages\CreateHistorialPedidos::route('/create'),
            'edit' => Pages\EditHistorialPedidos::route('/{record}/edit'),
        ];
    }
}
