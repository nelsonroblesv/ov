<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\CustomerStatementResource;
use Filament\Pages\Page;
use Filament\Tables;
use App\Models\Pedido;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VisitasSemana extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.app.pages.visitas-semana';
    protected static ?string $title = 'Ruta Semanal';
    protected static ?string $slug = 'ruta-semanal';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationGroup = 'Rutas & Visitas';
    protected static ?string $navigationLabel = 'Semana';
    protected static ?string $breadcrumb = "Ruta Semanal";
    protected static ?int $navigationSort = 1;


    public function table(Tables\Table $table): Tables\Table
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
            ->heading('Semana: ' .  Carbon::now()->startOfWeek(Carbon::MONDAY)->isoFormat('dddd D [de] MMMM, YYYY').' - '.Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4)->isoFormat('dddd D [de] MMMM, YYYY'))
            ->description('Lista de visitas a realizar durante la semana.')
            ->emptyStateHeading('No hay visitas programadas.')
            ->defaultSort('fecha_entrega', 'ASC')
            ->columns([
                TextColumn::make('num_ruta')
                    ->label('# Ruta')
                    ->alignCenter(),

                TextColumn::make('fecha_entrega')
                    ->label('Fecha')
                    ->sortable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),

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
/*
                IconColumn::make('visitado')
                    ->label('Visitado')
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        return $record->visitas()->whereDate('fecha_visita', now()->toDateString())->exists();
                    })
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
 */                   
            ])
            ->filters([])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('view_invoice')
                        ->label('Estado de Cuenta')
                        ->color('info')
                        ->icon('heroicon-o-document-chart-bar')
                        ->url(fn($record) => CustomerStatementResource::getUrl(name: 'invoice', parameters: ['record' => $record->customer]))
                        ->openUrlInNewTab(),
                ])
            ]);
    }
}
