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
            ->modifyQueryUsing(function ($query) {
                $hoy = strtoupper(Carbon::now()->format('D'));
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

                $query->whereIn('zonas_id', function ($subQuery) use ($diaActual, $user) {
                    $subQuery->select('id')
                        ->from('zonas')
                        ->where('dia_zona', $diaActual)
                        ->where('user_id', $user); // Filtra las zonas del día actual
                });
            })
            ->defaultSort('created_at', 'desc')
            ->heading('Itinerario de visitas hoy')
            ->description('')
            ->columns([
                TextColumn::make('name')->label('Cliente o Identificador'),
                TextColumn::make('user.name')->label('Vendedor'),
                TextColumn::make('regiones.name')->label('Región'),
                TextColumn::make('zonas.nombre_zona')->label('Zona'),
                ColorColumn::make('zonas.color_zona')->label('Color de Zona')->alignCenter(),
                TextColumn::make('tipo_cliente')->label('Tipo de Cliente')->alignCenter()
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
