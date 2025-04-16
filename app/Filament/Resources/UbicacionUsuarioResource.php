<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UbicacionUsuarioResource\Pages;
use App\Filament\Resources\UbicacionUsuarioResource\RelationManagers;
use App\Models\UbicacionUsuario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UbicacionUsuarioResource extends Resource
{
    protected static ?string $model = UbicacionUsuario::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
       
        // Hide table from Resource
        return $table
            ->columns([])
            ->content(null)
            ->paginated(false);
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
            'index' => Pages\ListUbicacionUsuarios::route('/'),
            'create' => Pages\CreateUbicacionUsuario::route('/create'),
            'edit' => Pages\EditUbicacionUsuario::route('/{record}/edit'),
        ];
    }
}
