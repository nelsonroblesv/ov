<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaqueteGuiaResource\Pages;
use App\Filament\Resources\PaqueteGuiaResource\RelationManagers;
use App\Models\PaqueteGuia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaqueteGuiaResource extends Resource
{
    protected static ?string $model = PaqueteGuia::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Logistica';
    protected static ?string $navigationLabel = 'Paquetes de Guías';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaqueteGuias::route('/'),
            'create' => Pages\CreatePaqueteGuia::route('/create'),
            'edit' => Pages\EditPaqueteGuia::route('/{record}/edit'),
        ];
    }
}
