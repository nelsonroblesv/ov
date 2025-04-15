<?php

namespace App\Filament\Resources\CobranzaResource\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction as ActionsCreateAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagosRelationManager extends RelationManager
{
    protected static string $relationship = 'pagos';
    protected static ?string $title = 'Pagos realizados';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles del pago')->schema([

                    Select::make('tipo_semana')
                        ->label('Tipo de semana:')
                        ->placeholder('Selecciona el tipo de semana')
                        ->required()
                        ->options([
                            '0' => 'PAR',
                            '1' => 'NON'
                        ]),

                    TextInput::make('periodo')
                        ->label('Periodo:')
                        ->placeholder('Pj. P15')
                        ->maxLength(3)
                        ->required()
                        ->autocapitalize(),

                    Select::make('semana')
                        ->label('Semana:')
                        ->required()
                        ->options([
                            'S1' => 'S1',
                            'S2' => 'S2',
                            'S3' => 'S3',
                            'S4' => 'S4'
                        ]),

                    Select::make('tipo_pago')
                        ->label('Tipo de pago')
                        ->required()
                        ->options([
                            'Efectivo' => 'Efectivo',
                            'Transferencia' => 'Transferencia',
                            'Depósito' => 'Depósito',
                            'Cheque' => 'Cheque',
                            'Otro' => 'Otro',
                        ])
                        ->native(false), // Estilo select moderno

                    DateTimePicker::make('created_at')
                        ->label('Fecha de pago')
                        ->seconds(false)
                        ->required()
                        ->default(now()), // Coloca fecha y hora actual automáticamente

                    TextInput::make('monto')
                        ->label('Monto del Pago')
                        ->required()
                        ->numeric()
                        ->prefix('$'),

                    FileUpload::make('comprobante')
                        ->label('Comprobante (PDF o Imagen)')
                        ->directory('comprobantes')
                        //->required()
                        ->columnSpanFull()
                        //->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->downloadable()
                        ->openable(),
                ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Pagos')
            ->columns([
                TextColumn::make('periodo')->label('Periodo')->sortable()->alignCenter(),
                TextColumn::make('semana')->label('Semana')->sortable()->alignCenter(),
                TextColumn::make('tipo_semana')->label('Tipo')->sortable()->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        0 => 'PAR',
                        1 => 'NON'
                    ][$state] ?? 'Otro')
                    ->colors([
                        'success' => 0,
                        'info' => 1,
                    ])->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Fecha de pago')
                    ->date()
                    ->timezone('America/Merida'),

                TextColumn::make('tipo_pago')
                    ->label('Tipo de pago')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('MXN')
                    ->summarize(Sum::make()->label('Total pagado')),

                TextColumn::make('comprobante')
                    ->label('Archivo')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar Pago')
                    ->icon('heroicon-o-banknotes'),
            ])
            ->actions([
               ActionsActionGroup::make([
                Tables\Actions\CreateAction::make()
                ->successNotification(null)
                ->label('Registrar Pago')
                ->after(function ($record) {
                    Notification::make()
                        ->title('Pago registrado')
                        ->body("Se registró un pago por \$" . number_format($record->monto, 2))
                        ->success()
                        ->icon('heroicon-o-banknotes')
                        ->send();
                }),
                Tables\Actions\EditAction::make()
                ->successNotification(null)
                ->label('Editar Pago')
                ->after(function ($record) {
                    Notification::make()
                        ->title('Información de Pago actualizada')
                        ->body("Se actualizó la informacion de forma exitosa.")
                        ->success()
                        ->icon('heroicon-o-banknotes')
                        ->send();
                }),
                Tables\Actions\DeleteAction::make(),
               ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
