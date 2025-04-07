<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdministrarRutasResource\Pages;
use App\Filament\Resources\AdministrarRutasResource\RelationManagers;
use App\Models\AdministrarRutas;
use App\Models\Customer;
use App\Models\GestionRutas;
use Doctrine\DBAL\Query\QueryException;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class AdministrarRutasResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Rutas';
    protected static ?string $navigationLabel = 'Administrar Rutas';
    protected static ?string $breadcrumb = "Administrar Rutas";
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
            ->recordUrl(null)
            ->heading('Registros no asignados')
            ->description('Clientes/Prospectos que no están asignados a ninguna ruta.')
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereNotIn('id', function ($subQuery) {
                    $subQuery->select('customer_id')
                        ->from('gestion_rutas');
                    //->where('user_id', $userId);
                });
            })
            ->columns([
                TextColumn::make('user.name')->label('Usuario')->searchable()->sortable(),
                TextColumn::make('name')->label('Iidentificador')->searchable()->sortable(),
                TextColumn::make('zona.dia_zona')->label('Dia')->searchable()->sortable()
                    ->badge()
                    ->colors([
                        'info' => 'Lun',
                        'warning' => 'Mar',
                        'danger' => 'Me',
                        'success' => 'Jue',
                        'custom_light_blue' => 'Vie',
                    ]),
                TextColumn::make('zona.tipo_semana')->label('Semana')->searchable()->sortable()
                    ->badge()
                    ->colors([
                        'success' => 'PAR',
                        'danger' => 'NON',
                    ])
                    ->icons([
                        'heroicon-o-arrow-long-down' => 'PAR',
                        'heroicon-o-arrow-long-up' => 'NON',
                    ]),
                TextColumn::make('tipo_cliente')->label('Tipo')->badge()
                    ->colors([
                        'success' => 'PV',
                        'danger' => 'RD',
                        'custom_black' => 'BK',
                        'custom_gray' => 'SL',
                        'danger' => 'PO',
                        'warning' => 'PR'
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'PV' => 'Punto Venta',
                        'RD' => 'Red',
                        'BK' => 'Black',
                        'SL' => 'Silver',
                        'PO' => 'Posible',
                        'PR' => 'Prospecto'
                    ][$state] ?? 'Otro'),
                TextColumn::make('regiones.name')->label('Region')->searchable()->sortable(),
                TextColumn::make('zona.nombre_zona')->label('Zona')->searchable()->sortable(),
                TextColumn::make('full_address')->label('Dirección')->searchable()->sortable(),
            ])
            ->filters([])
            ->actions([
                ActionsAction::make('agregarARuta')
                    ->icon('heroicon-o-truck')
                    ->label('Agregar a Ruta')
                    ->form([
                        Select::make('dia_semana')
                            ->label('Día de la Semana')
                            ->options([
                                'Lun' => 'Lunes',
                                'Mar' => 'Martes',
                                'Mie' => 'Miércoles',
                                'Jue' => 'Jueves',
                                'Vie' => 'Viernes',
                            ])
                            ->required(),
                        Radio::make('tipo_semana')
                            ->label('Tipo de Semana')
                            ->options([
                                'PAR' => 'PAR',
                                'NON' => 'NON',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, Customer $record): void {
                        // Manejo de errores
                        try {
                            GestionRutas::insert([
                                'user_id' =>  $record->user_id,
                                'dia_semana'  => $data['dia_semana'],
                                'tipo_semana' => $data['tipo_semana'],
                                'customer_id' => $record->id,
                                'region_id'   => $record->regiones_id, // << debe estar aquí
                                'zona_id'     => $record->zonas_id,    // << debe estar aquí
                            ]);
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title('Error al agregar el cliente a la ruta.')
                                ->body($e->getMessage())
                                ->send();
                            return; // Salta a la siguiente iteración
                        }

                        Notification::make()
                            ->success()
                            ->title('Cliente ' . $record->name . ' agregado a la ruta.')
                            ->send();
                    }),
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([]);
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
            'index' => Pages\ListAdministrarRutas::route('/'),
            'create' => Pages\CreateAdministrarRutas::route('/create'),
            'edit' => Pages\EditAdministrarRutas::route('/{record}/edit'),
        ];
    }
}
