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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                    ->withSum(
                        ['orders as monto_total' => function (Builder $q) {
                            $q->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
                                ->where('is_active', true);
                        }],
                        'grand_total'
                    );
            })
            ->defaultSort('monto_total', 'DESC')
            ->columns([
                TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('monto_total')
                    ->label('Saldo Total')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2))
                    ->color(function ($state) {
                        if ($state == 0.00) {
                            return 'info';      // Azul
                        } elseif ($state > 0.00) {
                            return 'warning';   // Amarillo
                        } elseif ($state < 0.00) {
                            return 'success';   // Verde
                        }
                        return null;
                    })
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
