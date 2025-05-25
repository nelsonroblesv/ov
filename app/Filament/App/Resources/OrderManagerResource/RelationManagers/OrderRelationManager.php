<?php

namespace App\Filament\App\Resources\OrderManagerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';
    protected static ?string $title = 'Pedidos';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    //protected static ?string $navigationGroup = 'Pedidos & Pagos';
    protected static ?string $navigationLabel = 'Gestionar Pedidos';
    protected static ?string $breadcrumb = "Gestionar Pedidos";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->heading('Pedidos del Cliente')
            ->emptyStateDescription('No hay Pedidos registrados para este cliente.')
            ->columns([
                TextColumn::make('number')->label('# Pedido'),
                TextColumn::make('created_at')->label('Fecha')->date(),
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
