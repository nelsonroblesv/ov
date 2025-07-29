<?php

namespace App\Filament\App\Resources\BitacoraCustomersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CobroRelationManager extends RelationManager
{
    protected static string $relationship = 'cobros';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading('Cobros de la Visita')
            ->columns([

                TextColumn::make('fecha_pago')->badge()->color('info'),
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
                    
                IconColumn::make('aprobado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->label('Aprobado')
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
