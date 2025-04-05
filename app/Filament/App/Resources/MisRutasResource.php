<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MisRutasResource\Pages;
use App\Filament\App\Resources\MisRutasResource\RelationManagers;
use App\Models\GestionRutas;
use App\Models\MisRutas;
use Doctrine\DBAL\Query\QueryException;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
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
    protected static ?string $navigationLabel = 'Gestionar Rutas';
    protected static ?string $breadcrumb = "Gestionar Ruta";
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
            ->heading('Mis Rutas')
            ->description('Estas son tus Rutas. Utiliza los controles disponibles para organizar tus rutas.
            Puedes arrastrar y soltar para cambiar el orden de las rutas. Seleccionar 
            el dia, filtrar por Tipo de semana y buscar por nombre de cliente.')
            ->reorderable('orden')
            ->columns([
                TextColumn::make('orden')->label('Orden')->sortable(),
                //TextColumn::make('user.name')->label('Nombre')->searchable(),
                TextColumn::make('customer.name')->label('Cliente')->searchable(),
                TextColumn::make('customer.regiones.name')->label('Region'),
                TextColumn::make('customer.zona.nombre_zona')->label('Zona'),
                TextColumn::make('customer.tipo_cliente')->label('Tipo Cliente'),
                TextColumn::make('tipo_semana')->label('Dia')->searchable(),
                TextColumn::make('dia_semana')->label('Semana')->searchable(),
            ])
            ->filters([
                SelectFilter::make('tipo_semana')
                    ->label('Tipo Semana')
                    ->options([
                        'PAR' => 'PAR',
                        'NON' => 'NON',
                    ])
                    ->searchable(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Action::make('Cambiar Ruta')
                    ->label('Cambiar Ruta')
                    ->icon('heroicon-o-arrows-right-left')
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
                            'user_id' => auth()->id(),
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
