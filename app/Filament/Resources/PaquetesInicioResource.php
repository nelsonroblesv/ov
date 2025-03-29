<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaquetesInicioResource\Pages;
use App\Filament\Resources\PaquetesInicioResource\RelationManagers;
use App\Models\PaquetesInicio;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaquetesInicioResource extends Resource
{
    protected static ?string $model = PaquetesInicio::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Catalogo';
    protected static ?string $navigationLabel = 'Paquetes de Inicio';
    protected static ?string $breadcrumb = "Paquetes de Inicio";
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Paquete de Inicio')->schema([
                    TextInput::make('prefijo')
                        ->label('Prefijo')
                        ->required(),
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required(),
                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->required(),
                    FileUpload::make('imagen')
                        ->label('Imagen')
                        ->directory('paquetes-inicio')
                        ->required(),
                    TextInput::make('precio')
                        ->label('Precio')
                        ->numeric()
                        ->placeholder('0.00')
                        ->suffixIcon('heroicon-m-currency-dollar')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('Imagen')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prefijo')
                    ->label('Prefijo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('precio')
                    ->label('Precio')
                    ->searchable()
                    
                    ->prefix('$')
                    ->sortable(),   
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
            'index' => Pages\ListPaquetesInicios::route('/'),
            'create' => Pages\CreatePaquetesInicio::route('/create'),
            'edit' => Pages\EditPaquetesInicio::route('/{record}/edit'),
        ];
    }
}
