<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
               TextColumn::make('number')
               ->label('Num. Orden')
               ->searchable()
               ->sortable()
               ->copyable()
                ->copyMessage('Orden copiada')
                ->copyMessageDuration(1500),

               TextColumn::make('status')
               ->badge()
               ->colors([
                'primary',
                'info' => 'pending',
                'warning' => 'processing',
                'success' => 'completed',
                'danger' => 'declined',
            ])
            ->icons([
                'heroicon-o-x',
                'heroicon-o-clock' => 'pending',
                'heroicon-o-x-mark' => 'declined',
                'heroicon-o-check' => 'completed',
                'heroicon-o-arrow-path' => 'processing'
            ]),

            TextColumn::make('grand_total')
            ->label('Importe')
            ->money()
            ->sortable(),

            TextColumn::make('created_at')
            ->label('Fecha de Orden')
            ->date()
            ->searchable()
            ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
              
            ])
            ->actions([
               // Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
