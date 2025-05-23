<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerOrdersResource\Pages\Invoice;
use App\Filament\Resources\CustomerStatementResource\Pages;
use App\Filament\Resources\CustomerStatementResource\RelationManagers;
use App\Models\Customer;
use App\Models\CustomerStatement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerStatementResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $title = 'Estado de Cuenta';
    protected static ?string $slug = 'estados-cuenta';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Estados de Cuenta';
    protected static ?string $breadcrumb = "Estados de Cuenta";
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query
                    ->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
                    ->where('is_active', true);
            })
            ->recordUrl(null)
            ->defaultSort('name', 'ASC')
            ->heading('Estados de Cuenta')
            ->description('')
            ->columns([
                TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
               // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_invoice')
                    ->label('Estado de Cuenta')
                    ->icon('heroicon-o-document-chart-bar')
                    ->url(function($record){
                       return self::getUrl('invoice',['record'=>$record]);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCustomerStatements::route('/'),
            'create' => Pages\CreateCustomerStatement::route('/create'),
            'edit' => Pages\EditCustomerStatement::route('/{record}/edit'),
            'invoice' => Pages\Invoice::route('/{record}/invoice'),
        ];
    }
}
