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
use App\Models\Customer;
use App\Models\OrderItem;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Administrar Pedidos';
    protected static ?string $breadcrumb = "Pedidos";
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Detalles del Pedido')
                        ->schema([
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

                            TextInput::make('number')
                                ->label('Numero de Orden')
                                ->required()
                                ->disabled()
                                ->default('OR-' . random_int(100000, 9999999))
                                ->dehydrated()
                                ->maxLength(255),

                            ToggleButtons::make('status')
                                ->label('Estado del Pedido')
                                ->required()
                                ->inline()
                                ->options([
                                    'PEN' => 'Pendiente',
                                    'COM'  => 'Completo',
                                    'REC'  => 'Rechazado',
                                    'REU'  => 'Reubicar',
                                    'DEV'  => 'Devuelta Parcial',
                                    'SIG'  => 'Siguiente Visita'
                                ])
                                ->colors([
                                    'PEN' => 'info',
                                    'COM' => 'warning',
                                    'REC' => 'success',
                                    'REU' => 'danger',
                                    'DEV' => 'success',
                                    'SIG' => 'danger'
                                ])
                                ->icons([
                                    'PEN' => 'heroicon-o-exclamation-circle',
                                    'COM' => 'heroicon-o-check',
                                    'REC' => 'heroicon-o-x-mark',
                                    'REU' => 'heroicon-o-map-pin',
                                    'DEV' => 'heroicon-o-archive-box-arrow-down',
                                    'SIG' => 'heroicon-o-calendar-date-range',
                                ])
                                ->default('PEN')
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
                        'info' => 'PEN',
                        'success' => 'COM',
                        'danger' => 'REC',
                        'custom' => 'REU',
                        'warning' => 'DEV',
                        'info' => 'SIG',
                    ])
                    ->icons([
                        'heroicon-o-exclamation-circle' =>  'PEN',
                        'heroicon-o-check' => 'COM',
                        'heroicon-o-x-mark' => 'REC',
                        'heroicon-o-map-pin' => 'REU',
                        'heroicon-o-archive-box-arrow-down' => 'DEV',
                        'heroicon-o-calendar-date-range' => 'SIG',
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'PEN' => 'PENDIENTE',
                        'COM' => 'COMPLETADO',
                        'REC' => 'RECHAZADO',
                        'REU' => 'REUBICADO',
                        'DEV' => 'DEV PARCIAL',
                        'SIG' => 'SIG VISITA',
                    ][$state] ?? 'Otro'),

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
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Pedido eliminado')
                                ->body('El Pedido ha sido eliminado del sistema.')
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
                        ->modalDescription('Estas seguro que deseas eliminar los Pedidos seleccionados? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
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

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'Administrador';
    }

    protected static ?string $navigationBadgeTooltip = 'Pedidos Pendientes';
}
