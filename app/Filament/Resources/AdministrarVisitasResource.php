<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdministrarVisitasResource\Pages;
use App\Filament\Resources\AdministrarVisitasResource\RelationManagers;
use App\Models\AdministrarVisitas;
use App\Models\EntregaCobranzaDetalle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdministrarVisitasResource extends Resource
{
    protected static ?string $model = EntregaCobranzaDetalle::class;

    protected static ?string $title = 'Administrar Visitas';
    protected static ?string $slug = 'administrar-visitas';
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Agenda de Trabajo';
    protected static ?string $navigationLabel = 'Administrar Visitas';
    protected static ?string $breadcrumb = "Administrar Visitas";
    protected static ?int $navigationSort = 1;

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
            ->recordTitleAttribute('id')
            ->emptyStateDescription('No hay registros para mostrar')
            ->defaultSort('fecha_programada', 'DESC')
            ->columns([
                TextColumn::make('fecha_programada')->label('Fecha')->date(),

               TextColumn::make('customer_id')
                    ->label('Ubicaciones')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $region = $record->customer?->regiones?->name ?? 'Sin región';
                        $zona = $record->customer?->zona?->nombre_zona ?? 'Sin zona';

                        return "<span>📍 {$region}</span><br>
                                <span>📌 {$zona}</span>";
                    }),
                    
                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('tipo_visita')
                    ->label('Tipo')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => [
                        'PR' => 'Prospecto',
                        'PO' => 'Posible',
                        'EP' => 'Entrega Primer Pedido',
                        'ER' => 'Entrega Recurrente',
                        'CO' => 'Cobranza',
                    ][$state] ?? 'Otro')
                    ->colors([
                        'danger' => 'PR',
                        'warning' => 'PO',
                        'info' => 'EP',
                        'success' => 'ER',
                        'primary' => 'CO',
                    ]),

                TextColumn::make('user.name')
                    ->label('Colaborador')
                    ->searchable(),

                TextColumn::make('notas_admin')
                    ->label('Notas (Admin)')
                    ->toggleable(isToggledHiddenByDefault:true),

                TextColumn::make('notas_colab')
                    ->label('Notas (Colab)')
                    ->toggleable(isToggledHiddenByDefault:true),

                IconColumn::make('status')
                    ->label('Visitado')
                    ->sortable()
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('fecha_visita')->label('Visita')->date()
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->alignCenter(),

                  ToggleColumn::make('is_verified')
                    ->label('Verificado')
                    ->sortable()
                    ->alignCenter(),

            ])
            ->filters([
                //
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
            'index' => Pages\ListAdministrarVisitas::route('/'),
            'create' => Pages\CreateAdministrarVisitas::route('/create'),
            'edit' => Pages\EditAdministrarVisitas::route('/{record}/edit'),
        ];
    }
}
