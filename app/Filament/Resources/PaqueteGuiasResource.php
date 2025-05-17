<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaqueteGuiasResource\Pages;
use App\Filament\Resources\PaqueteGuiasResource\RelationManagers;
use App\Models\PaqueteGuias;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaqueteGuiasResource extends Resource
{
    protected static ?string $model = PaqueteGuias::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Logística';
    protected static ?string $navigationLabel = 'Paquete de Guias';
    protected static ?string $breadcrumb = "Paquete de Guías";
    protected static ?int $navigationSort = 0;

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
            'create' => Pages\CreatePaqueteGuias::route('/create'),
            'edit' => Pages\EditPaqueteGuias::route('/{record}/edit'),
        ];
    }
}
