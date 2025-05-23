<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerOrdersResource\Pages;
use App\Filament\Resources\CustomerOrdersResource\RelationManagers;
use App\Filament\Resources\CustomerOrdersResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use App\Models\CustomerOrders;
use App\Models\Payments;
use App\Models\User;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerOrdersResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $title = 'Pedidos';
    protected static ?string $slug = 'pedidos';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Gestionar Pedidos';
    protected static ?string $breadcrumb = "Gestionar Pedidos";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informacion del Cliente')->schema([
                    TextInput::make('name')
                        ->label('Cliente')
                        ->disabled()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query
                    ->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
                    ->where('is_active', true)
                    ->withSum('orders as total_ordenes', 'grand_total')
                    ->withSum(
                        ['payments as total_pagos' => function ($q) {
                            $q->where('is_verified', true);
                        }],
                        'importe'
                    );
            })
            ->defaultSort('total_ordenes', 'DESC')
            ->heading('Lista de Pedidos por Cliente')
            ->description('TOTAL: Indica el importe total del o los Pedidos realizados por los clientes. PAGOS: Suma de los importes de cada pago generado y que ha sido verificado. SALDO: Diferencia entre el Total y los Pagos. El saldo a favor se indica con A FAVOR. *NOTA: Es importante marcar los Pagos como Verificados para que se reflejen correctamente en el saldo.')
            ->columns([
                TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('total_ordenes')->label('Total')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2)),
                TextColumn::make('total_pagos')->label('Pagos')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2)),
                TextColumn::make('saldo_pendiente')
                    ->label('Saldo')
                    ->badge()
                    ->getStateUsing(
                        fn($record) =>
                        $record->total_ordenes - $record->total_pagos
                    )
                    ->color(function ($state) {
                        $state = (float) $state;

                        if ($state == 0) {
                            return 'black';
                        } elseif ($state > 0) {
                            return 'danger';
                        } else {
                            return 'success';
                        }
                    })
                    ->formatStateUsing(function ($state) {
                        $valor = (float) $state;
                        $texto = '$ ' . number_format($valor, 2);

                        if ($valor < 0) {
                            $texto .= ' (A FAVOR)';
                        }

                        return $texto;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Detalles')
                    ->color('info'),

                Tables\Actions\Action::make('registrar-pago')
                    ->label('Registrar Pago')
                    ->color('success')
                    ->icon('heroicon-o-banknotes')
                    ->form([
                        Section::make('Informacion del Pago')->schema([

                            Select::make('user_id')
                                ->required()
                                ->label('Registrado por:')
                                ->options(
                                    fn() =>
                                    User::whereIn('id', function ($query) {
                                        $query->select('id')
                                            ->from('users')
                                            ->where('is_active', true)
                                            ->where('role', 'Vendedor')
                                            ->orWhere('username', 'OArrocha')
                                            ->orderBy('name', 'DESC');
                                    })->pluck('name', 'id')
                                ),

                            TextInput::make('importe')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('0.00'),

                            Select::make('tipo')
                                ->options([
                                    'E' => 'Efectivo',
                                    'T' => 'Transferencia',
                                    'O' => 'Otro',
                                ])
                                ->default('E')
                                ->required(),

                            DatePicker::make('created_at')
                                ->label('Fecha de pago')
                                ->default(Carbon::now())
                                ->required(),

                            FileUpload::make('voucher')
                                ->label('Comprobante')
                                ->directory('recibos')
                                ->columnSpanFull(),

                            Textarea::make('notas')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])->columns(2)

                    ])
                    ->modalHeading('Registrar Pago')
                    ->modalDescription('Registrar un nuevo pago del cliente.')
                    ->modalSubmitActionLabel('Registrar Pago')
                    ->action(function (array $data, Customer $record): void {
                        // Manejo de errores
                        try {
                           Payments::insert([
                                'customer_id' =>  $record->id,
                                'user_id'  => $data['user_id'],
                                'importe' => $data['importe'],
                                'tipo' => $data['tipo'],
                                'voucher'   => $data['voucher'],
                                'notas'     => $data['notas'],
                                'is_verified'     => true,
                                'created_at'     => $data['created_at'],
                            ]);
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title('Error al registrar el Pago.')
                                ->body($e->getMessage())
                                ->send();
                            return;
                        }
                        Notification::make()
                            ->success()
                            ->title('Pago registrado.')
                            ->body('Se ha registrado un Pago por '.number_format($data['importe'],2).' al cliente '.$record->name .' de manera exitosa con fecha '.$data['created_at'].'.')
                            ->icon('heroicon-o-banknotes')
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerOrders::route('/'),
            'create' => Pages\CreateCustomerOrders::route('/create'),
            'edit' => Pages\EditCustomerOrders::route('/{record}/edit'),

        ];
    }
}
