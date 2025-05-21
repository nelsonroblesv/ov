<?php

namespace App\Filament\Resources\CustomerOrdersResource\RelationManagers;

use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles')->schema([
                    TextInput::make('number')
                        ->label('Numero de Pedido')
                        ->default('POV-' . random_int(100000, 9999999))
                        ->required()
                        ->maxLength(255)
                        ->suffixIcon('heroicon-m-hashtag')
                        ->unique(ignoreRecord: true)
                        ->disabledOn('edit'),

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
                            'PEN' => 'Pendiente',
                            'COM'  => 'Completo',
                            'REC'  => 'Rechazado',
                            'REU'  => 'Reubicar',
                            'DEV'  => 'Devuelta Parcial',
                            'SIG'  => 'Siguiente Visita'
                        ]),

                    DatePicker::make('created_at')
                        ->label('Fecha')
                        ->default(Carbon::now())
                        ->required()
                        ->native(),

                    DatePicker::make('fecha_liquidacion')
                        ->label('Fecha de liquidación')
                        ->default(Carbon::now()->addDays(15))
                        ->native(),

                    TextInput::make('notes')
                        ->label('Notas adicionales del Pedido')
                        ->nullable()
                        ->suffixIcon('heroicon-m-pencil-square'),

                    TextInput::make('grand_total')
                        ->label('Importe')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('0.00')
                        ->suffixIcon('heroicon-m-currency-dollar'),

                    Select::make('solicitado_por')
                        ->required()
                        ->label('Solicitado por:')
                        ->options(
                            User::query()
                                ->where('is_active', true)
                                ->where('role', 'Vendedor')
                                ->pluck('name', 'id')
                        ),

                    FileUpload::make('notas_venta')
                        ->label('Notas de Venta')
                        ->placeholder('Haz click para cargar la(s) nota(s) de venta')
                        ->multiple()
                        ->directory('notas_venta')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),

                    Hidden::make('registrado_por')->default(fn() => auth()->id()),

                ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->heading('Pedidos del Cliente')
            ->columns([
                TextColumn::make('number')->label('# Pedido'),
                TextColumn::make('grand_total')->label('Importe')->alignRight()
                     ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2))
                    ->summarize(
                        Sum::make()->label('Total')
                            ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2))),
                TextColumn::make('status')->label('Estatus')->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'PEN' => 'Pendiente',
                        'COM' => 'Completo',
                        'REC' => 'Rechazado',
                        'REU' => 'Reubicar',
                        'DEV' => 'Devuelto Parcial',
                        'SIG' => 'Siguiente Visita'
                    ][$state] ?? 'Otro')
                    ->colors([
                        'warning' => 'PEN',
                        'success' => 'COM',
                        'danger' => 'REC',
                        'info' => 'REU',
                        'primary' => 'DEV',
                        'secondary' => 'SIG'
                    ]),
                TextColumn::make('created_at')->label('Fecha')->date(),
                TextColumn::make('solicitador.name')->label('Vendedor')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Pedido')
                     ->modalHeading('Nuevo Pedido de Cliente')
                    ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Pedido registrado.')
                                ->body('Se ha agregado un nuevo Pedido al cliente de forma correcta.')
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->color('success')
                        ),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar Pedido')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Pedido actualizado')
                                ->body('La información del Pedido ha sido actualizada.')
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->color('success')
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->label('Eliminar')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Pedido eliminado')
                                ->body('El Pedido del cliente ha sido eliminado.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('success')
                                ->color('success')
                        )
                        ->modalHeading('Borrar Pedido de Cliente')
                        ->modalDescription('Estas seguro que deseas eliminar este Pedido? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
