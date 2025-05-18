<?php

namespace App\Filament\Resources\PaqueteGuiasResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuiasRelationManager extends RelationManager
{
    protected static string $relationship = 'guias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Guía')->schema([
                    TextInput::make('numero_guia')
                        ->required()
                        ->maxLength(255),
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_guia')
            ->columns([
                TextColumn::make('numero_guia')->label('Número de guía'),
                TextColumn::make('recibido')->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        '0' => 'No recibido',
                        '1' => 'Recibido'
                    ][$state] ?? 'Otro')
                    ->colors([
                        'danger' => 0,
                        'success' => 1,
                    ])
                    ->sortable()
                    ->summarize(Count::make()->label('Total de Cajas')),
                TextColumn::make('created_at')->label('Fecha de registro')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Guia')
                    ->icon('heroicon-o-document-check')
                    ->modalHeading(('Agregar Guia'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Guía registrada correctamente')
                            ->body('La Guía ha sido agregada al paquete actual.')
                            ->icon('heroicon-o-document-check')
                            ->iconColor('success')
                            ->color('success')
                    ),

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Guía actualizada correctamente')
                            ->body('La Guía ha sido actualizada.')
                            ->icon('heroicon-o-check-circle')
                            ->iconColor('success')
                            ->color('success')
                    )
                    ->modalHeading('Editar Guía'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Guía eliminada')
                            ->body('La Guía ha sido eliminada del paquete.')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->color('danger')
                    )
                    ->modalHeading('Borrar Guía')
                    ->modalDescription('Estas seguro que deseas eliminar esta Guía? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Si, eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
