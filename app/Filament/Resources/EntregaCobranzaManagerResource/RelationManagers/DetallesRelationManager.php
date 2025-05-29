<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\RelationManagers;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Regiones;
use App\Models\User;
use App\Models\Zonas;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles')->schema([
                    DatePicker::make('fecha_programada')->label('Fecha programada:')
                        ->required()
                        ->default(Carbon::now()),

                    Select::make('tipo_visita')
                        ->label('Tipo:')
                        ->required()
                        ->options([
                            'PR' => 'Prospecto',
                            'PO' => 'Posible',
                            'EP' => 'Entrega Primer Pedido',
                            'ER' => 'Entrega Recurrente',
                            'CO' => 'Cobranza',
                        ]),

                    Select::make('user_id')
                        ->label('Colaborador asignado:')
                        ->required()
                        ->options(
                            User::query()
                                ->where('is_active', true)
                                ->where('role', 'Vendedor')
                                ->orderBy('name', 'ASC')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),

                    // Por el momento se hace manual ya que no hay un orden establecido sobre los clientes
                    // de cada colaborador. Mas adelante al elegir el cliente, se cargará de forma automatica 
                    // el colaborador

                    Select::make('customer_id')
                        ->label('Cliente, Prospecto o Posible:')
                        ->required()
                        ->options(
                            Customer::query()
                                ->where('is_active', true)
                                ->orderBy('name', 'ASC')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),

                    Textarea::make('notas')
                        ->label('Notas (Administración')
                        ->nullable()
                        ->columnSpanFull()

                ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->emptyStateDescription('No hay registros para mostrar')
            ->defaultSort('fecha_programada', 'ASC')
            ->columns([
                TextColumn::make('fecha_programada')->label('Fecha')->date(),

                TextColumn::make('customer.regiones.name')
                    ->label('Region')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault:false),

                TextColumn::make('customer.zona.nombre_zona')
                    ->label('Zona')
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault:false),
                    
                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('tipo_visita')
                    ->label('Tipo')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'PR' => 'Prospecto',
                        'PO' => 'Posible',
                        'EP' => 'Entrega Primer Pedido',
                        'ER' => 'Entrega Recurrente',
                        'CO' => 'Cobranza',
                    ][$state] ?? 'Otro')
                    ->colors([
                        'Danger' => 'PR',
                        'warning' => 'PO',
                        'info' => 'EP',
                        'success' => 'ER',
                        'primary' => 'CO',
                    ]),

                TextColumn::make('user.name')
                    ->label('Colaborador')
                    ->searchable(),

                TextColumn::make('notas_admin')
                    ->label('Notas (Admin)')
                    ->toggleable(isToggledHiddenByDefault:true),

                TextColumn::make('notas_colab')
                    ->label('Notas (Colab)')
                    ->toggleable(isToggledHiddenByDefault:true),

                IconColumn::make('status')
                    ->label('Estatus')
                    ->sortable()
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('fecha_visita')->label('Visita')->date()
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->alignCenter(),

                  ToggleColumn::make('is_verified')
                    ->label('Verificado')
                    ->sortable()
                    ->alignCenter(),

            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Vendedor')
                    ->options(
                        User::query()
                            ->where('is_active', true)
                            ->where('role', 'Vendedor')
                            ->orderBy('name', 'ASC')
                            ->pluck('name', 'id')
                    ),

                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'E' => 'Entrega',
                        'C' => 'Cobranza'
                    ])
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Visita')
                    ->icon('heroicon-o-list-bullet')
                    ->modalHeading('Nueva Visita Programada')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Registro agregado.')
                            ->body('Se ha registrado una nueva Visita de forma correcta.')
                            ->icon('heroicon-o-check-circle')
                            ->iconColor('success')
                            ->color('success')
                    ),
            ])
            ->actions([
                ActionsActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalHeading('Editar Visita')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Registro actualizado')
                                ->body('La información del registro ha sido actualizada.')
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->color('success')
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Registro eliminado')
                                ->body('El registro ha sido eliminado de forma correcta.')
                                ->icon('heroicon-o-trash')
                                ->iconColor('success')
                                ->color('success')
                        )
                        ->modalHeading('Borrar registro')
                        ->modalDescription('Estas seguro que deseas eliminar este registro? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Si, eliminar'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //  Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
