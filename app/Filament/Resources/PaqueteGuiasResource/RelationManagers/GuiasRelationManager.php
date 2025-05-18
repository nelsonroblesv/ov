<?php

namespace App\Filament\Resources\PaqueteGuiasResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
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
                    ->modalHeading(('Agregar Guia'))
                /* ->form([
                        TextInput::make('numero_guia')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('foto_caja')
                            ->label('Foto de la caja')
                            ->image()
                            ->required()
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/*'])
                            ->directory('guias')
                            ->preserveFilenames(),
                        Toggle::make('recibido')
                            ->label('Recibido')
                            ->default(false)
                    ])->modalHeading('Agregar Guia'
            */
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
