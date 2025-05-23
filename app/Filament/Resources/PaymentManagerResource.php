<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentManagerResource\Pages;
use App\Filament\Resources\PaymentManagerResource\RelationManagers;
use App\Models\Customer;
use App\Models\PaymentManager;
use App\Models\Payments;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentManagerResource extends Resource
{
    protected static ?string $model = Payments::class;

    protected static string $relationship = 'payments';
    protected static ?string $title = 'Pagos';
    protected static ?string $slug = 'pagos';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Administrar Pagos';
    protected static ?string $breadcrumb = "Administrar Pagos";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informacion del Pago')->schema([

                    Select::make('customer_id')
                        ->label('Cliente')
                        ->options(Customer::query()
                            ->where('is_active', true)
                            ->orderBy('name', 'ASC')
                            ->pluck('name', 'id'))
                        ->preload()
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),

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

                    Hidden::make('is_verified')
                        ->default(true)
                        ->dehydrated(true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'DESC')
            ->columns([
                TextColumn::make('customer.name')->label('Cliente')->searchable(),
                TextColumn::make('created_at')->label('Fecha de Pago')->date(),
                TextColumn::make('importe')->label('Importe')
                    ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2)),
                TextColumn::make('tipo')->label('Tipo de Pago')->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'E' => 'Efectivo',
                        'T' => 'Transferencia',
                    ][$state] ?? 'Otro')
                    ->colors([
                        'success' => 'E',
                        'warning' => 'T',
                        'info' => 'O'
                    ]),
                ToggleColumn::make('is_verified')->label('Verificado'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPaymentManagers::route('/'),
            'create' => Pages\CreatePaymentManager::route('/create'),
            'edit' => Pages\EditPaymentManager::route('/{record}/edit'),
        ];
    }
}
