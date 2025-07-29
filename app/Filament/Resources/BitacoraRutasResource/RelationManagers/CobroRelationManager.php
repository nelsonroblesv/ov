<?php

namespace App\Filament\Resources\BitacoraRutasResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CobroRelationManager extends RelationManager
{
    protected static string $relationship = 'cobros';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

     public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading('Cobros de la Visita')
            ->columns([

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->badge()
                    ->color('info'),
                TextColumn::make('monto')->badge()->color('success')
                    ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2)),
                TextColumn::make('tipo_pago')
                    ->label('Tipo de Pago')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'EF' => 'Efectivo',
                        'TR' => 'Transferencia',
                        'CH' => 'Cheque',
                        default => 'Otro',
                    })
                    ->alignCenter()
                    ->badge(),
                TextColumn::make('comentarios'),
                    
                ToggleColumn::make('aprobado')
                    ->label('Aprobar')
                    ->alignCenter(),
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
