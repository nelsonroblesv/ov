<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\GestionRutasResource\Pages;
use App\Filament\App\Resources\GestionRutasResource\RelationManagers;
use App\Models\Customer;
use App\Models\GestionRutas;
use Doctrine\DBAL\Query\QueryException;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GestionRutasResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Rutas';
    protected static ?string $navigationLabel = 'Agregar Clientes a Ruta';
    protected static ?string $breadcrumb = "Agregar Clientes a Ruta";
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
            ->modifyQueryUsing(function (Builder $query) {
                $userId = auth()->id();

                $query->where('user_id', $userId)
                    ->whereNotIn('id', function ($subQuery) use ($userId) {
                        $subQuery->select('customer_id')
                            ->from('gestion_rutas')
                            ->where('user_id', $userId);
                    });
            })
            ->columns([
                //TextColumn::make('user.name')->label('Nombre')->searchable()->sortable(),

                TextColumn::make('name')->label('Iidentificador')->searchable()->sortable(),
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
			TextColumn::make('zona.dia_zona')->label('Dia')->searchable()->sortable()
				->badge()
				->colors([
					'info' => 'Lun',
					'warning' => 'Mar',
					'danger' => 'Me',
					'success' => 'Jue',
					'custom_light_blue' => 'Vie',
				]),
                // TextColumn::make('email')->label('Correo')->searchable()->sortable(),
                // TextColumn::make('phone')->label('Teléfono')->searchable()->sortable(),
                TextColumn::make('regiones.name')->label('Region')->searchable()->sortable(),
                TextColumn::make('zona.nombre_zona')->label('Zona')->searchable()->sortable(),
                TextColumn::make('full_address')->label('Dirección')->searchable()->sortable(),
                // Agrega aquí 
            ])
            ->filters([])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('agregarARuta')
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
                        ->action(function (array $data, Collection $records): void {
                            foreach ($records as $customer) {
                                // Validación de existencia en rutas_planificadas
                                if (GestionRutas::where([
                                    'user_id' => auth()->id(),
                                    'dia_semana' => $data['dia_semana'],
                                    'tipo_semana' => $data['tipo_semana'],
                                    'customer_id' => $customer->id,
                                    'region_id' => $customer->regiones_id,
                                    'zona_id' => $customer->zonas_id,
                                ])->exists()) {
                                    Notification::make()
                                        ->warning()
                                        ->title('El cliente ' . $customer->name . ' ya está en la ruta.')
                                        ->send();
                                    continue; // Salta a la siguiente iteración
                                }

                                // Manejo de errores
                                try {
                                    GestionRutas::insert([
                                        'user_id'     => auth()->id(),
                                        'dia_semana'  => $data['dia_semana'],
                                        'tipo_semana' => $data['tipo_semana'],
                                        'customer_id' => $customer->id,
                                        'region_id'   => $customer->regiones_id, // << debe estar aquí
                                        'zona_id'     => $customer->zonas_id,    // << debe estar aquí
                                    ]);
                                } catch (QueryException $e) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Error al agregar el cliente a la ruta.')
                                        ->body($e->getMessage())
                                        ->send();
                                    continue; // Salta a la siguiente iteración
                                }

                                Notification::make()
                                    ->success()
                                    ->title('Cliente ' . $customer->name . ' agregado a la ruta.')
                                    ->send();
                            }
                        }),
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
          //  'index' => Pages\ListGestionRutas::route('/'),
          //  'create' => Pages\CreateGestionRutas::route('/create'),
          //  'edit' => Pages\EditGestionRutas::route('/{record}/edit'),
        ];
    }
}
