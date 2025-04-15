<?php

namespace App\Filament\Resources\CobranzaResource\RelationManagers;

use Filament\Actions\CreateAction as ActionsCreateAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
                        ->options([
                            '0' => 'PAR',
                            '1' => 'NON'
                        ]),

                    TextInput::make('periodo')
                        ->label('Periodo:')
                        ->placeholder('Pj. P15')
                        ->maxLength(3)
                        ->autocapitalize(),

                    Select::make('semana')
                        ->label('Semana:')
                        ->options([
                            '1' => 'S1',
                            '2' => 'S2',
                            '3' => 'S3',
                            '4' => 'S4'
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
                        ->displayFormat('Y-m-d') // Formato visual en el formulario
                        ->seconds(false)
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
                TextColumn::make('created_at')
                    ->label('Fecha de pago')
                    ->dateTime()
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
