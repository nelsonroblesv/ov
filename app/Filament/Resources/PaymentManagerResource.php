<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentManagerResource\Pages;
use App\Filament\Resources\PaymentManagerResource\RelationManagers;
use App\Models\Cobro;
use App\Models\Customer;
use App\Models\PaymentManager;
use App\Models\Payments;
use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;
use Closure;
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
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PaymentManagerResource extends Resource
{
    protected static ?string $model = Cobro::class;

    protected static string $relationship = 'cobros';
    protected static ?string $title = 'Pagos';
    protected static ?string $slug = 'pagos';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Administrar Pagos';
    protected static ?string $breadcrumb = "Administrar Pagos";
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = true;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cliente')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Clientes')
                            ->options(Customer::query()
                                ->where('is_active', true)
                                ->whereIn('tipo_cliente', ['PV', 'RD', 'BK', 'SL'])
                                ->whereHas('pedidos', function ($query) {
                                    $query->where('estado_general', 'abierto');
                                })
                                ->orderBy('name', 'ASC')
                                ->pluck('name', 'id'))
                            ->live()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn(callable $set) => $set('pedido_id', null))
                            ->disabledOn('edit')
                            ->columns(2),

                        Select::make('pedido_id')
                            ->label('Pedido')
                            ->options(function (callable $get) {
                                $customerId = $get('customer_id');
                                if (!$customerId) {
                                    return [];
                                }

                                return Pedido::where('customer_id', $customerId)
                                    ->orderBy('created_at', 'desc')
                                    ->pluck('id_nota', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabledOn('edit')
                    ])->columns(2),

                Section::make('Cobranza')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Hidden::make('user_id')->default(fn() => Auth::id()),
                        Hidden::make('visita_id')->default(fn() => null),
                        Hidden::make('fecha_pago')->default(fn() => Carbon::now()),

                        TextInput::make('monto')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('0.00'),

                        Select::make('tipo_pago')
                            ->options([
                                'EF' => 'Efectivo',
                                'TR' => 'Transferencia',
                                'CH' => 'Cheque',
                                'DP' => 'Depósito',
                                'OT' => 'Otro',
                            ])
                            ->default('EF')
                            ->required(),

                        Textarea::make('comentarios')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('comprobantes')
                            ->label('Comprobante')
                            ->directory('comprobantes-cobros')
                            ->columnSpanFull()
                            ->multiple()
                            ->required()
                            ->downloadable(),

                        Hidden::make('aprobado')
                            ->default(false)
                            ->dehydrated(true),

                        Select::make('aprobado')
                            ->label('Aprobar pago')
                            ->options([
                                1 => 'Aprobado',
                                0 => 'Pendiente',
                            ])
                            ->default(0)
                            ->required()
                            ->reactive()
                            ->columns(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->heading('Pagos')
            ->description('Lista de Pagos registrados.')
            ->defaultSort('id', 'DESC')
            ->columns([
                TextColumn::make('created_at')->label('Fecha de Pago')->dateTime(),
                TextColumn::make('pedido.id_nota')->label('ID Nota'),
                TextColumn::make('pedido.customer.name')->label('Cliente')->searchable(),
                TextColumn::make('monto')->label('Monto')
                    ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2)),
                TextColumn::make('tipo_pago')->label('Tipo de Pago')->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'EF' => 'Efectivo',
                        'TR' => 'Transferencia',
                        'CH' => 'Cheque',
                        'DP' => 'Depósito',
                        'OT' => 'Otro',
                    ][$state] ?? 'Otro')
                    ->alignCenter(),
                TextColumn::make('user.name')->label('Registró'),
                ImageColumn::make('comprobantes')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),

                TextColumn::make('comentarios')->label('Comentarios'),

                ToggleColumn::make('aprobado')->label('Aprobar')
                    ->alignCenter(),
            ])
            ->filters([])
            ->actions([
                ActionGroup::make([
                    EditAction::make()->label('Editar'),
                    DeleteAction::make()->label('Eliminar')
                ])
            ])
            ->bulkActions([]);
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
