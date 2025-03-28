<?php

namespace App\Filament\Resources\PaqueteGuiaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
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
               Section::make('Datos de la Guía')
                    ->schema([
                        TextInput::make('numero_guia')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_guia')
            ->columns([
                TextColumn::make('numero_guia')->searchable()->sortable(),
                IconColumn::make('revisado')
                    ->boolean()
                    ->label('Revisado'),

                TextColumn::make('estado')->badge()
                    ->colors([
                        'secondary' => 'Pendiente',
                        'danger' => 'Incidencia',
                        'success' => 'Revisado',
                    ])
                    ->sortable()
                    ->summarize(Sum::make()->label('Cantidad de Paquetes')),
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
