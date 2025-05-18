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
    protected static ?string $navigationLabel = 'Pedidos de Clientes';
    protected static ?string $breadcrumb = "Pedidos de Clientes";

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
                $query->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
                    ->where('is_active', true)
                    ->orderBy('name', 'asc');
            })


            ->columns([
                TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
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
