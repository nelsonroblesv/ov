<?php

namespace App\Filament\Resources\CobranzaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
                TextInput::make('monto')
                    ->label('Monto del Pago')
                    ->required()
                    ->numeric()
                    ->prefix('$'),

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

                FileUpload::make('comprobante')
                    ->label('Comprobante (PDF o Imagen)')
                    ->directory('comprobantes')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->downloadable()
                    ->openable(),
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

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('MXN')
                    ->summarize(Sum::make()->label('Total pagado')),

                TextColumn::make('tipo_pago')
                    ->label('Tipo de pago')
                    ->searchable()
                    ->sortable(),

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
