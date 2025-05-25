<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaCobranzaResource\Pages;
use App\Filament\Resources\EntregaCobranzaResource\RelationManagers;
use App\Models\EntregaCobranza;
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

class EntregaCobranzaResource extends Resource
{
    protected static ?string $model = EntregaCobranza::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos generales')->schema([
                    DatePicker::make('fecha_programada')
                    ->required()
                    ->default(Carbon::now()),

                Hidden::make('alta_user_id')->default(fn() => auth()->id()),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_programada')
                    ->date()
                    ->sortable(),
                TextColumn::make('altaUser.name')
                    ->label('Registrado por'),
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
            'index' => Pages\ListEntregaCobranzas::route('/'),
            'create' => Pages\CreateEntregaCobranza::route('/create'),
            'edit' => Pages\EditEntregaCobranza::route('/{record}/edit'),
        ];
    }
}
