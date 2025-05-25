<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\RelationManagers;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Columns\TextColumn;
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
                    Select::make('customer_id')
                        ->label('Cliente')
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

                    Select::make('user_id')
                        ->label('Vendedor')
                        ->required()
                        ->options(
                            User::query()
                                ->where('is_active', true)
                                ->where('role', 'Vendedor')
                                ->orderBy('name', 'ASC')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('tipo')
                        ->label('Tipo')
                        ->required()
                        ->options([
                            'E' => 'Entrega',
                            'C' => 'Cobranza',
                        ]),

                    Textarea::make('notas')
                        ->label('Notas')
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
            ->columns([
                TextColumn::make('customer.regiones.name')
                    ->label('Region')
                    ->sortable(),

                TextColumn::make('customer.zona.nombre_zona')
                    ->label('Zona')
                    ->sortable(),

                TextColumn::make('customer.name'),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'E' => 'Entrega',
                        'C' => 'Cobranza',

                    ][$state] ?? 'Otro')
                    ->colors([
                        'success' => 'E',
                        'warning' => 'C'
                    ]),

                TextColumn::make('user.name')
                    ->label('Vendedor')
                    ->searchable(),

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
                    ->label('Registrar Entrega/Cobranza')
                    ->icon('heroicon-o-calendar-days')
                    ->modalHeading('Nueva Entrega/Cobranza')
                    ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Registro agregado.')
                                ->body('Se ha registrado una nueva Entrega/Cobranza de forma correcta.')
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->color('success')
                        ),
            ])
            ->actions([
                ActionsActionGroup::make([
                    Tables\Actions\EditAction::make()
                    ->modalHeading('Editar Entrega/Cobranza')
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
