<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ItinerarioResource\Pages;
use App\Filament\App\Resources\ItinerarioResource\RelationManagers;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItinerarioResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Clientes y Prospectos';
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
            ->modifyQueryUsing(function ($query) {
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

                $query
                ->select('*')
                ->from('zonas')
                ->where('user_id', $user)
                ->where('dia_zona', $diaActual)
                ->where('tipo_semana', 'PAR')
                ->orderBy('regiones_id', 'asc');
            })
            //->defaultSort('created_at', 'desc')
            ->heading('Itinerario de visitas')
            ->description('Lista de visitas programadas para hoy')
            ->columns([
                TextColumn::make('nombre_zona')->label('Cliente o Identificador'),
                TextColumn::make('user.name')->label('Vendedor'),
                TextColumn::make('zonas.dia_zona')->label('Dia'),
                TextColumn::make('regiones.name')->label('Región'),
                TextColumn::make('zonas.nombre_zona')->label('Zona'),
                ColorColumn::make('zonas.color_zona')->label('Color de Zona')->alignCenter(),
                IconColumn::make('full_address')->label('Ubicación')->alignCenter()
                            ->icon('heroicon-o-map-pin')
                            ->color('danger')
                            ->url(fn ($record) => "https://www.google.com/maps/search/?api=1&query=" . urlencode($record->full_address), true)
                            ->openUrlInNewTab(),
                TextColumn::make('tipo_cliente')->label('Tipo de Visita')->badge()->alignCenter()
                    ->colors([
                        'danger' => 'PO',
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
            'index' => Pages\ListItinerarios::route('/'),
            'create' => Pages\CreateItinerario::route('/create'),
            'edit' => Pages\EditItinerario::route('/{record}/edit'),
        ];
    }
}
