<?php

namespace App\Filament\Resources;

use App\Enums\PaymentTypeEnum;
use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Administrar';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $breadcrumb = "Pagos";
  //  protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informacion de Cliente')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Select::make('customer_id')
                                    ->options(Customer::all()->pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->reactive()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(fn(callable $set) => $set('order_id', null)),

                                Select::make('order_id')
                                    ->options(fn(Get $get): Collection => Order::query()
                                        ->where('customer_id', $get('customer_id'))
                                        ->where([
                                            ['status', '<>', 'completed'],
                                            ['status', '<>', 'declined']
                                        ])
                                        ->pluck('number', 'id'))
                                        ->reactive()
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->required(),

                                Select::make('type')
                                        ->options([
                                           'cash' => PaymentTypeEnum::CASH->value,
                                           'transfer' => PaymentTypeEnum::TRANSFER->value
                                    ])
                            ])->columnSpanFull()
                    ]),
                    
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Datos de Pago')
                        ->icon('heroicon-o-currency-dollar')
                        ->live()
                        ->schema([
                            Placeholder::make('grand_total')
                                ->label('Saldo')
                                ->content(
                                    function(Get $get, Set $set){
                                        $total = 0;
                                        $total = $get('order_id');
                                        $order = Order::find($get('order_id')) ?? 0;
                                        if($order){
                                            $order = $order->grand_total;
                                        }
                                        $set('grand_total', $order);
                                    return Number::currency($order, 'USD');
                                    })
                                ->extraAttributes(['style' => 'text-align:right']),
                            
                            Forms\Components\TextInput::make('amount')
                                    ->required()
                                    ->minValue(1)
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->prefixIcon('heroicon-m-currency-dollar')
                                    ->prefixIconColor('success')
                                    ->extraInputAttributes(['style' => 'text-align:right']),

                            Forms\Components\FileUpload::make('voucher')
                                ->image()
                                ->required()
                                ->imageEditor()
                                ->directory('invoices-images'),

                            Forms\Components\Textarea::make('notes')
                                ->columnSpanFull()
                        ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->heading('Pagos')
        ->description('Administrador de realizados.')
            ->columns([
                TextColumn::make('order.number')
                    ->label('Num. Orden'),
                TextColumn::make('customer.name')
                    ->label('Cliente'),
                TextColumn::make('amount')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable()
                    ->money('USD'),
                TextColumn::make('order.grand_total')
                    ->label('Saldo')
                    ->numeric()
                    ->money(),
                TextColumn::make('type')
                    ->label('Tipo de pago')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->colors([
                        'info' => 'transfer',
                        'success' => 'cash',
                    ])
                    ->icons([
                        'heroicon-o-credit-card' => 'transfer',
                        'heroicon-o-banknotes' => 'cash'
                    ]),
                TextColumn::make('notes')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('voucher')
                    ->label('Comprobante')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
            'view' => Pages\ViewPayment::route('/{record}')
        ];
    }
}
