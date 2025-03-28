<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RutasUsuariosResource\Pages;
use App\Filament\Resources\RutasUsuariosResource\RelationManagers;
use App\Models\Regiones;
use App\Models\Rutas;
use App\Models\RutasUsuarios;
use App\Models\User;
use App\Models\Zonas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RutasUsuariosResource extends Resource
{
    protected static ?string $model = Rutas::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Bitacora';
    protected static ?string $navigationLabel = 'Rutas de Usuarios';
    protected static ?string $breadcrumb = 'Rutas de Usuarios';
    protected static ?int $navigationSort = 5;
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
                TextColumn::make('user.name')->label('Usuario')->searchable()->sortable(),
                TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('regiones.name')->label('Region')->searchable()->sortable(),
                TextColumn::make('zonas.nombre_zona')->label('Zona')->searchable()->sortable(),
                TextColumn::make('tipo_semana')->label('Semana')->searchable()->sortable(),
                TextColumn::make('tipo_cliente')->label('Tipo Cliente')->searchable()->sortable(),
                IconColumn::make('visited')->label('Visitado')->sortable()
                    ->boolean(),
                TextColumn::make('created_at')->label('Alta')
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('user_id')->label('Usuarios')
                    ->options(User::pluck('name', 'id'))
                    ->multiple(),

                SelectFilter::make('tipo_semana')->label('Semana')
                    ->options([
                        'PAR' => 'PAR',
                        'NON' => 'NON',
                    ])
                    ->multiple(),

                SelectFilter::make('tipo_cliente')->label('Tipo Cliente')
                    ->options([
                        'PV' => 'Punto de Venta',
                        'RD' => 'Red',
                        'BK' => 'Black',    
                        'SL' => 'Silver',
                    ])
                    ->multiple(),

                SelectFilter::make('regiones_id')->label('Regiones')
                    ->options(Regiones::pluck('name', 'id'))
                    ->multiple(),

                SelectFilter::make('zonas_id')->label('Zonas')
                    ->options(Zonas::pluck('nombre_zona', 'id'))
                    ->multiple(),
            ])
            ->actions([
               // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListRutasUsuarios::route('/'),
            'create' => Pages\CreateRutasUsuarios::route('/create'),
            'edit' => Pages\EditRutasUsuarios::route('/{record}/edit'),
        ];
    }
}
