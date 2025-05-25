<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaCobranzaManagerResource\Pages;
use App\Filament\Resources\EntregaCobranzaManagerResource\RelationManagers;
use App\Filament\Resources\EntregaCobranzaManagerResource\RelationManagers\DetallesRelationManager as RelationManagersDetallesRelationManager;
use App\Filament\Resources\EntregaCobranzaResource\RelationManagers\DetallesRelationManager;
use App\Models\EntregaCobranza;
use App\Models\EntregaCobranzaManager;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntregaCobranzaManagerResource extends Resource
{
    protected static ?string $model = EntregaCobranza::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos Generales')->schema([
                    DatePicker::make('fecha_programada')
                        ->label('Fecha programada')
                        ->required()
                        ->unique(ignoreRecord:true)
                        ->validationMessages([
                            'unique' => 'Ya existe una entrega programada para esta fecha. Selecciona otra o edita la existente.'
                        ])
                        ->default(Carbon::now())
                        ->disabledOn('edit'),

                    Hidden::make('alta_user_id')->default(fn() => auth()->id()),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_programada', 'DESC')
            ->columns([
                TextColumn::make('fecha_programada')
                        ->label('Fecha programada')
                        ->date(),
                 TextColumn::make('altaUser.name')
                        ->label('Registrado por')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagersDetallesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntregaCobranzaManagers::route('/'),
            'create' => Pages\CreateEntregaCobranzaManager::route('/create'),
            'edit' => Pages\EditEntregaCobranzaManager::route('/{record}/edit'),
        ];
    }
}
