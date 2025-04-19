<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MisRutasResource\Pages;
use App\Filament\App\Resources\MisRutasResource\RelationManagers;
use App\Models\GestionRutas;
use App\Models\MisRutas;
use Doctrine\DBAL\Query\QueryException;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MisRutasResource extends Resource
{
    protected static ?string $model = GestionRutas::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';
    protected static ?string $navigationGroup = 'Rutas';
    protected static ?string $navigationLabel = 'Administrar Rutas';
    protected static ?string $breadcrumb = "Administrar Ruta";
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
        ->modifyQueryUsing(function (Builder $query) {
            $query->join('customers', 'gestion_rutas.customer_id', '=', 'customers.id')
                  ->where('customers.is_active', true)
                  ->where('gestion_rutas.user_id', auth()->user()->id) 
                  ->select('gestion_rutas.*');
        })
            ->recordUrl(null)
            ->heading('Mis Rutas')
            ->description('Estas son tus Rutas. Utiliza los controles disponibles para organizar tus rutas.
            Puedes arrastrar y soltar para cambiar el orden de las rutas. Seleccionar 
            el dia, filtrar por tipo de semana y buscar por nombre de cliente.')
            ->reorderable('orden')
            ->columns([
                TextColumn::make('orden')->label('Orden')->sortable(),
                //TextColumn::make('user.name')->label('Nombre')->searchable(),
                TextColumn::make('customer.name')->label('Cliente')->searchable(),
                TextColumn::make('customer.regiones.name')->label('Region'),
                TextColumn::make('customer.zona.nombre_zona')->label('Zona'),
                TextColumn::make('customer.tipo_cliente')->label('Tipo Cliente')->badge()
                    ->colors([
                        'success' => 'PV',
                        'danger' => 'RD',
                        'custom_black' => 'BK',
                        'custom_gray' => 'SL',
                        'danger' => 'PO',
                        'warning' => 'PR'
                    ])
                    ->icons([
                        'heroicon-o-building-storefront' => 'PV',
                        'heroicon-o-user' => 'RD',
                        'heroicon-o-star' => 'BK',
                        'heroicon-o-sparkles' => 'SL'
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'PV' => 'Punto Venta',
                        'RD' => 'Red',
                        'BK' => 'Black',
                        'SL' => 'Silver',
                        'PO' => 'Posible',
                        'PR' => 'Prospecto',
                    ][$state] ?? 'Otro'),

                TextColumn::make('tipo_semana')->label('Semana')->searchable()->sortable()
                    ->badge()
                    ->colors([
                        'success' => 'PAR',
                        'danger' => 'NON',
                    ])
                    ->icons([
                        'heroicon-o-arrow-long-down' => 'PAR',
                        'heroicon-o-arrow-long-up' => 'NON',
                    ]),
                TextColumn::make('dia_semana')->label('Dia')->searchable()->sortable()
                    ->badge()
                    ->colors([
                        'info' => 'Lun',
                        'warning' => 'Mar',
                        'danger' => 'Me',
                        'success' => 'Jue',
                        'custom_light_blue' => 'Vie',
                    ]),
            ])
            ->filters([
                SelectFilter::make('tipo_semana')
                    ->label('Tipo Semana')
                    ->options([
                        'PAR' => 'PAR',
                        'NON' => 'NON',
                    ]),
            ])
            ->actions([])
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
            'index' => Pages\ListMisRutas::route('/'),
            'create' => Pages\CreateMisRutas::route('/create'),
            'edit' => Pages\EditMisRutas::route('/{record}/edit'),
        ];
    }
}
