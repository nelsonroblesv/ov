<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\OrderManagerResource\Pages;
use App\Filament\App\Resources\OrderManagerResource\RelationManagers;
use App\Filament\App\Resources\OrderManagerResource\RelationManagers\OrderRelationManager;
use App\Filament\App\Resources\OrderManagerResource\RelationManagers\PaymentsRelationManager;
use App\Models\Customer;
use App\Models\OrderManager;
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

class OrderManagerResource extends Resource
{
    protected static ?string $model = Customer::class;

   protected static ?string $title = 'Pedidos';
    protected static ?string $slug = 'pedidos';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Gestionar Pedidos';
    protected static ?string $breadcrumb = "Gestionar Pedidos";
     protected static ?int $navigationSort = 2;

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
                    ->where('user_id', auth()->user()->id)
                    ->withSum('orders as total_ordenes', 'grand_total')
                    ->withSum(
                        ['payments as total_pagos' => function ($q) {
                            $q->where('is_verified', true);
                        }],
                        'importe'
                    );
            })
            ->defaultSort('total_ordenes', 'DESC')
            ->heading('Pedidos')
            ->description('Lista de Pedidos de mis Clientes.')
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
                    ->label('Ver Detalles')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
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
            OrderRelationManager::class,
            PaymentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderManagers::route('/'),
            'create' => Pages\CreateOrderManager::route('/create'),
            'edit' => Pages\EditOrderManager::route('/{record}/edit'),
        ];
    }
}
