<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ItinerarioResource\Pages;
use App\Filament\App\Resources\ItinerarioResource\RelationManagers;
use App\Models\AsignarTipoSemana;
use App\Models\BitacoraCustomers;
use App\Models\Customer;
use App\Models\Rutas;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItinerarioResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';
    protected static ?string $navigationGroup = 'Bitacora';
    protected static ?string $navigationLabel = 'Itinerario';
    protected static ?string $breadcrumb = "Itinerario";
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
                $hoy = strtoupper(Carbon::now()->setTimezone('America/Merida')->format('D'));
                $dias = [
                    'MON' => 'Lun',
                    'TUE' => 'Mar',
                    'WED' => 'Mie',
                    'THU' => 'Jue',
                    'FRI' => 'Vie',
                    'SAT' => 'Sab',
                    'SUN' => 'Dom',
                ];
                $diaActual = $dias[$hoy];
                $user = auth()->id();
                $tipoSemanaSeleccionado = AsignarTipoSemana::value('tipo_semana');
                $valores = [
                    '0' => 'PAR',
                    '1' => 'NON',
                ];
                $semana = $valores[$tipoSemanaSeleccionado];

                $query->where('user_id', $user)
                    ->whereHas('zonas', function ($q) use ($diaActual, $user, $semana) {
                        $q->where('dia_zona', $diaActual)
                            ->where('tipo_semana', $semana)
                            ->where('user_id', $user);
                        // ->where('tipo_semana', 'PAR'); 
                    });
            })
            ->defaultSort('created_at', 'desc')

            ->heading('Itinerario de visitas')
            ->description('Esta es la lista de visitas asignadas para hoy ' . Carbon::now()->setTimezone('America/Merida')->locale('es')->translatedFormat('l d \d\e F Y'). 
                            ' Recuerda agregar cada una para crear tu Ruta.')
            ->columns([
                TextColumn::make('name')->label('Cliente o Identificador'),
                TextColumn::make('tipo_cliente')->label('tipo'),
                TextColumn::make('regiones.name')->label('Región'),
                TextColumn::make('zonas.nombre_zona')->label('Zona'),
                TextColumn::make('zonas.tipo_semana')->label('Semana')->badge()->alignCenter()
                    ->colors([
                        'success' => 'PAR',
                        'danger' => 'NON',
                    ]),
                TextColumn::make('full_address')->label('Direccion'),
                TextColumn::make('tipo_cliente')->label('Tipo')->badge()->alignCenter()
                    ->colors([
                        'gray' => 'PO',
                        'warning' => 'PR',
                        'success' => 'PV',
                        'danger' => 'RD',
                        'info' => 'BK',
                        'warning' => 'SL'
                    ])
                    ->icons([
                        'heroicon-o-map' => 'PO',
                        'heroicon-o-magnifying-glass' => 'PR',
                        'heroicon-o-building-storefront' => 'PV',
                        'heroicon-o-user' => 'RD',
                        'heroicon-o-star' => 'BK',
                        'heroicon-o-sparkles' => 'SL'
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'PO' => 'Posible',
                        'PR' => 'Prospecto',
                        'PV' => 'Punto Venta',
                        'RD' => 'Red',
                        'BK' => 'Black',
                        'SL' => 'Silver',
                    ][$state] ?? 'Otro'),
            ])
            ->filters([])
            ->actions([
                Action::make('Agregar a Ruta')
                    ->icon('heroicon-o-map')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->hidden(function ($record) {
                        return Rutas::where('user_id', auth()->id())
                            ->where('customer_id', $record->id)
                            ->whereDate('created_at', Carbon::now()->setTimezone('America/Merida')->toDateString()) // Mismo día
                            ->exists();
                    })
                    ->action(function ($record, array $data) {
                        Rutas::create([
                            'user_id'  => auth()->id(),
                            'customer_id' => $record->id,
                            'regiones_id' => $record->regiones_id,
                            'zonas_id' => $record->zonas_id,
                            'tipo_semana' => $record['zonas']['tipo_semana'],
                            'tipo_cliente' => $record->tipo_cliente,
                            'full_address' => $record->full_address,
                            'visited' => 0
                        ]);

                        Notification::make()
                            ->title('Registro agregado a la Ruta actual')
                            ->success()
                            ->send();
                    })
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
            'index' => Pages\ListItinerarios::route('/'),
            'create' => Pages\CreateItinerario::route('/create'),
            'edit' => Pages\EditItinerario::route('/{record}/edit'),
        ];
    }
}
