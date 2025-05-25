<?php

namespace App\Filament\Resources\EntregaCobranzaManagerResource\RelationManagers;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
            ->columns([
                TextColumn::make('zona')
                    ->label('Zona'),

                TextColumn::make('region')
                    ->label('Region'),

                TextColumn::make('customer.name'),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'E' => 'Entrega',
                        'C' => 'Cobranza',

                    ][$state] ?? 'Otro'),

                TextColumn::make('user.name')
                    ->label('Vendedor')
                    ->searchable(),

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
