<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CambiarRutasResource\Pages;
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

class CambiarRutasResource extends Resource
{
    protected static ?string $model = GestionRutas::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Rutas';
    protected static ?string $navigationLabel = 'Cambiar Rutas';
    protected static ?string $breadcrumb = "Cambiar Rutas";
    protected static ?int $navigationSort = 2;

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
            ->heading('Rutas de Usuarios')
            ->description('Se muestra el listado de Rutas de Usuarios. Usa los controles disponbles para
                Eliminar y/o Cambiar la Ruta.')
            // ->reorderable('orden')
            ->columns([
                //TextColumn::make('orden')->label('Orden')->sortable(),
                TextColumn::make('user.name')->label('Usuario')->searchable(),
                TextColumn::make('customer.name')->label('Cliente')->searchable(),
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
                TextColumn::make('customer.regiones.name')->label('Region'),
                TextColumn::make('customer.zona.nombre_zona')->label('Zona'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    //->multiple()
                    ->preload()
                    ->searchable(),

                SelectFilter::make('tipo_semana')
                    ->label('Tipo Semana')
                    ->options([
                        'PAR' => 'PAR',
                        'NON' => 'NON',
                    ])
                    ->searchable(),

                SelectFilter::make('dia_semana')
                    ->label('Día de la Semana')
                    ->options([
                        'Lun' => 'Lunes',
                        'Mar' => 'Martes',
                        'Mie' => 'Miércoles',
                        'Jue' => 'Jueves',
                        'Vie' => 'Viernes',
                    ])
                    ->searchable(),
            ])
            ->actions([
                ActionsActionGroup::make([
                    Action::make('Cambiar Ruta')
                        ->label('Cambiar Ruta')
                        ->icon('heroicon-o-arrows-right-left')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Cambiar Cliente de Ruta')
                        ->modalDescription('Estás seguro que deseas cambiar este registro de tu Ruta?')
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
                        ->action(function ($record, array $data) {
                            $rutaExistente = GestionRutas::where([
                                'user_id' => $record->user_id,
                                'customer_id' => $record->customer_id,
                            ])->first();

                            if ($rutaExistente) {
                                try {
                                    $rutaExistente->update([
                                        'dia_semana' => $data['dia_semana'],
                                        'tipo_semana' => $data['tipo_semana'],
                                    ]);
                                    Notification::make()
                                        ->title('Ruta actualizada')
                                        ->body('El Cliente ha sido cambiado de ruta de forma exitosa.')
                                        ->success()
                                        ->send();
                                } catch (QueryException $e) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('No se pudo cambiar la ruta. Intenta nuevamente.')
                                        ->danger()
                                        ->send();
                                }
                            }
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->label('Eliminar Ruta')
                        ->modalHeading('Borrar Cliente de Ruta')
                        ->modalDescription('Estás seguro que deseas eliminar este registro de tu Ruta? Esta acción
                  no se puede deshacer.'),
                ])
            ], position: ActionsPosition::BeforeCells)
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
            'index' => Pages\ListCambiarRutas::route('/'),
            'create' => Pages\CreateCambiarRutas::route('/create'),
            'edit' => Pages\EditCambiarRutas::route('/{record}/edit'),
        ];
    }
}
