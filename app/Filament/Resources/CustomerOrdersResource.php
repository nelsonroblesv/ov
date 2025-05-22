<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerOrdersResource\Pages;
use App\Filament\Resources\CustomerOrdersResource\RelationManagers;
use App\Filament\Resources\CustomerOrdersResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use App\Models\CustomerOrders;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerOrdersResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $title = 'Pedidos';
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
                    ->label('Detalle del Pedido'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //  Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'view' => Pages\ViewCustomerOrders::route('/{record}'),
        ];
    }
}
