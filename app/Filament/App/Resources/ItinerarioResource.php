<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ItinerarioResource\Pages;
use App\Filament\App\Resources\ItinerarioResource\RelationManagers;
use App\Filament\App\Resources\RutasResource\Pages\ListRutas;
use App\Models\AsignarTipoSemana;
use App\Models\BitacoraCustomers;
use App\Models\Customer;
use App\Models\Rutas;
use Carbon\Carbon;
use Filament\Facades\Filament;
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

                $query->whereHas('zonas', function ($q) use ($diaActual, $user, $semana) {
                    $q->where('dia_zona', $diaActual)
                        ->where('tipo_semana', $semana)
                        ->whereHas('users', function ($subquery) use ($user) {
                            $subquery->where('users.id', $user); // Filtrar por el usuario autenticado en la tabla pivote
                        });
                });
            })
            ->defaultSort('created_at', 'desc')

            ->heading('Itinerario de visitas')
            ->description('Esta es la lista de visitas asignadas para hoy ' . Carbon::now()->setTimezone('America/Merida')->locale('es')->translatedFormat('l d \d\e F Y') .
                '. Estas listo para comenzar?')

            ->headerActions([
                Action::make('Guardar en Rutas')
                    ->label('Iniciar Ruta')
                    ->icon('heroicon-m-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function () {
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

                        // Obtener los registros que se están mostrando en la tabla
                        $clientes = Customer::where('user_id', $user)
                            ->whereHas('zonas', function ($q) use ($diaActual, $user, $semana) {
                                $q->where('dia_zona', $diaActual)
                                    ->where('tipo_semana', $semana)
                                    ->where('user_id', $user);
                            })
                            ->get();

                        // Verificar si hay registros
                        if ($clientes->isEmpty()) {
                            Notification::make()
                                ->title('No hay registros para guardar')
                                ->danger()
                                ->send();
                            return;
                        }

                        foreach ($clientes as $cliente) {
                            Rutas::create([
                                'user_id'  => auth()->id(),
                                'customer_id' => $cliente->id,
                                'regiones_id' => $cliente->regiones_id,
                                'zonas_id' => $cliente->zonas_id,
                                'tipo_semana' => $cliente['zonas']['tipo_semana'],
                                'tipo_cliente' => $cliente->tipo_cliente,
                                'full_address' => $cliente->full_address,
                                'created_at' => Carbon::now()->setTimezone('America/Merida')->toDateString(),
                                'visited' => 0
                            ]);
                        }

                        Notification::make()
                            ->title('Iniciemos el recorrido. Adelante!')
                            ->success()
                            ->send();

                        return redirect(RutasResource::getUrl());
                    })

                    ->disabled(fn() => Rutas::where('user_id', auth()->id())
                        ->whereDate('created_at', Carbon::now()->setTimezone('America/Merida')->toDateString())
                        ->exists())
            ])
            ->columns([
                TextColumn::make('name')->label('Cliente o Identificador'),
                TextColumn::make('simbolo')->label('Simbolo')->badge()
                    ->colors([
                        'black',/*
					'custom' => 'SB',
					'success' => 'BB', 
					'success' => 'UN', 
					'success' => 'OS', 
					'success' => 'CR', 
					'success' => 'UB', 
					'success' => 'NC'*/
                    ])
                    ->icons([
                        'heroicon-o-scissors' => 'SB',
                        'heroicon-o-building-storefront' => 'BB',
                        'heroicon-o-hand-raised' => 'UN',
                        'heroicon-o-rocket-launch' => 'OS',
                        'heroicon-o-x-mark' => 'CR',
                        'heroicon-o-map-pin' => 'UB',
                        'heroicon-o-exclamation-triangle' => 'NC'
                    ])
                    ->formatStateUsing(fn(string $state): string => [
                        'SB' => 'Salón de Belleza',
                        'BB' => 'Barbería',
                        'UN' => 'Salón de Uñas',
                        'OS' => 'OSBERTH',
                        'CR' => 'Cliente Pedido Rechazado',
                        'UB' => 'Ubicación en Grupo',
                        'NC' => 'Ya no compran'
                    ][$state] ?? 'Otro'),
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
                        'warning' => 'PO',
                        'info' => 'PR',
                        'success' => 'PV',
                        'danger' => 'RD',
                        'custom_black' => 'BK',
                        'custom_gray' => 'SL'
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
                        //dd(Carbon::now()->setTimezone('America/Merida')->toDateString());
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
                            'created_at' => Carbon::now()->setTimezone('America/Merida')->toDateString(),
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
