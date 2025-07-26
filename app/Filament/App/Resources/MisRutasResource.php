<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MisRutasResource\Pages;
use App\Filament\App\Resources\MisRutasResource\RelationManagers;
use App\Models\GestionRutas;
use App\Models\MisRutas;
use App\Models\Pedido;
use Carbon\Carbon;
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
use Illuminate\Support\Facades\Auth;

class MisRutasResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $slug = 'ruta-semanal';
    protected static ?string $navigationGroup = 'Rutas';
    protected static ?string $navigationLabel = 'Itinerario Semanal';
    protected static ?string $breadcrumb = "Ruta Semanal";
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;

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
            ->query(
                Pedido::query()
                    ->where('distribuidor', Auth::id())
                    ->where('estado_general', 'abierto')
                    ->whereBetween('fecha_entrega', [
                        Carbon::now()->startOfWeek(Carbon::MONDAY),
                        Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4)
                    ])
            )
            ->recordUrl(null)
            ->heading('Semana: ' .  Carbon::now()->startOfWeek(Carbon::MONDAY)->isoFormat('dddd D [de] MMMM, YYYY') . ' - ' . Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4)->isoFormat('dddd D [de] MMMM, YYYY'))
            ->description('Lista de visitas a realizar durante la semana.')
            ->emptyStateHeading('No hay visitas programadas.')
            ->defaultSort('num_ruta', 'ASC')
            ->columns([
                TextColumn::make('num_ruta')
                    ->label('# Ruta')
                    ->alignCenter(),

                TextColumn::make('fecha_entrega')
                    ->label('Fecha')
                    ->date(),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->badge()
                    ->color('info'),

                TextColumn::make('region.name')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $region = $record->customer?->regiones?->name ?? 'Sin región';
                        $zona = $record->customer?->zona?->nombre_zona ?? 'Sin zona';
                        return "
                                <span>📍 {$region}</span><br>
                                <span>🗺️ {$zona}</span><br>";
                    }),

                TextColumn::make('customer.phone')
                    ->label('Telefono')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->url(fn($record) => 'https://wa.me/' . urlencode($record->customer->phone), true)
                    ->openUrlInNewTab(),

                TextColumn::make('customer.full_address')
                    ->label('Ubicación')
                    ->badge()
                    ->icon('heroicon-o-map-pin')
                    ->color('danger')
                    ->url(fn($record) => 'https://www.google.com/maps/search/?api=1&query=' . $record->customer->latitude . ',' . $record->customer->longitude, true),

            ])
            ->filters([])
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
