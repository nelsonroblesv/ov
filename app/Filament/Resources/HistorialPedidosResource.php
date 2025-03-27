<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\HistorialPedidosResource\Pages;
use App\Models\Customer;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HistorialPedidosResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Historial de Pedidos';
    protected static ?string $breadcrumb = "Historial de Pedidos";
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles del Pedido')->schema([

                    TextInput::make('number')
                        ->label('Numero de Pedido')
                        ->required()
                        ->maxLength(255)
                        ->suffixIcon('heroicon-m-hashtag')
                        ->unique(ignoreRecord: true)
                        ->disabledOn('edit'),

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

                    ToggleButtons::make('tipo_nota')
                        ->label('Tipo de Nota')
                        ->required()
                        ->options([
                            'Sistema' => 'Sistema',
                            'Remisión' => 'Remisión',
                        ])
                        ->inline()
                        ->default('Sistema')
                        ->colors([
                            'Sistema' => 'success',
                            'Remisión' => 'warning',
                        ])
                        ->icons([
                            'Sistema' => 'heroicon-o-arrow-left-end-on-rectangle',
                            'Remisión' => 'heroicon-o-arrow-right-end-on-rectangle',
                        ])
                        ->default('Sistema'),

                    Select::make('tipo_semana_nota')
                        ->label('Semana de la Nota')
                        ->required()
                        ->options([
                            'PAR' => 'PAR',
                            'NON' => 'NON',
                        ]),

                    Select::make('dia_nota')
                        ->label('Día de la Nota')
                        ->required()
                        ->options([
                            'Lunes' => 'Lunes',
                            'Martes' => 'Martes',
                            'Miercoles' => 'Miercoles',
                            'Jueves' => 'Jueves',
                            'Viernes' => 'Viernes',
                        ]),

                    Select::make('status')
                        ->label('Estado del Pedido')
                        ->required()
                        ->options([
                            'Pendiente' => 'Pendiente',
                            'Completado' => 'Completado',
                            'Rechazado' => 'Rechazado',
                            'Reubicar' => 'Reubicar',
                            'Devuelta Parcial' => 'Devuelta Parcial',
                            'Siguiente Visita' => 'Siguiente Visita'
                        ]),

                    DatePicker::make('created_at')
                        ->label('Fecha')
                        ->required()
                        ->native(),

                    DatePicker::make('fecha_liquidacion')
                        ->label('Fecha de liquidación')
                        ->required()
                        ->native(),

                    TextInput::make('notes')
                        ->label('Notas adicionales del Pedido')
                        ->nullable()
                        ->suffixIcon('heroicon-m-pencil-square'),

                    TextInput::make('grand_total')
                        ->label('Monto')
                        ->required()
                        ->numeric()
                        ->placeholder('0.00')
                        ->suffixIcon('heroicon-m-currency-dollar'),

                    FileUpload::make('notas_venta')
                        ->label('Notas de Venta')
                        ->placeholder('Haz click para cargar la(s) nota(s) de venta')
                        ->multiple()
                        ->directory('notas_venta')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull()
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->heading('Pedidos')
            ->description('Historial de Pedidos.')

            ->columns([
                TextColumn::make('number')
                    ->label('# Pedido')
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

                TextColumn::make('fecha_liquidacion')
                    ->label('Liquidación')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tipo_semana_nota')
                    ->label('Semana')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->colors([
                        'info' => 'PAR',
                        'danger' => 'NON',
                    ])
                    ->icons([
                        'heroicon-o-calendar-days'
                    ]),

                TextColumn::make('dia_nota')
                    ->label('Dia')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->colors([
                        'info' => 'Pendiente',
                        'success' => 'Completado',
                        'gray' => 'Rechazado',
                        'warning' => 'Reubicar',
                        'danger' => 'Devuelta Parcial',
                        'info' => 'Siguiente Visita'
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'Pendiente',
                        'heroicon-o-check' => 'Completado',
                        'heroicon-o-x-mark' => 'Rechazado',
                        'heroicon-o-megaphone' => 'Reubicar',
                        'heroicon-o-arrow-uturn-left' => 'Devuelta Parcial',
                        'heroicon-o-calendar-date-range' => 'Siguiente Visita'

                    ]),

                TextColumn::make('notes')
                    ->label('Notas')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('grand_total')
                    ->label('Total')
                    ->summarize(Sum::make()->label('Total'))
                    ->prefix('$')
            ])
            ->filters([
                SelectFilter::make('tipo_semana_nota')
                    ->label('Tipo de Semana')
                    ->options([
                        'PAR' => 'PAR',
                        'NON' => 'NON',
                    ]),
                SelectFilter::make('tipo_nota')
                    ->label('Tipo de Nota')
                    ->options([
                        'Sistema' => 'Sistema',
                        'Remisión' => 'Remisión',
                    ]),

                SelectFilter::make('dia_nota')
                    ->label('Dia')
                    ->multiple()
                    ->options([
                        'Lunes' => 'Lunes',
                        'Martes' => 'Martes',
                        'Miercoles' => 'Miercoles',
                        'Jueves' => 'Jueves',
                        'Viernes' => 'Viernes',
                    ])
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Action::make('Reporte Individual')
                        ->label('Reporte PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->url(fn(Order $record) => route('ReporteIndividual', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Pedido eliminado')
                                ->body('El Pedido ha sido eliminada del sistema.')
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
                        ->modalDescription('Estas seguro que deseas eliminar los Pedidos seleccionadas? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
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
            'index' => Pages\ListHistorialPedidos::route('/'),
            'create' => Pages\CreateHistorialPedidos::route('/create'),
            'edit' => Pages\EditHistorialPedidos::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'Administrador';
    }
}
