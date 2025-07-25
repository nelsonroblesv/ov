<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\CustomerStatementResource;
use Filament\Pages\Page;
use Filament\Tables;
use App\Models\EntregaCobranzaDetalle;
use App\Models\Pedido;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MisEntregas extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.app.pages.mis-entregas';
    protected static ?string $title = 'Ruta de Hoy';
    protected static ?string $slug = 'ruta-hoy';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Rutas & Visitas';
    protected static ?string $navigationLabel = 'Hoy';
    protected static ?string $breadcrumb = "Ruta Hoy";
    protected static ?int $navigationSort = 0;


    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Pedido::query()
                    ->where('distribuidor', Auth::id())
                    ->where('estado_general', 'abierto')
                    ->whereDate('fecha_entrega', '=', Carbon::now())
            )
            ->heading('Hoy ' . Carbon::now()->isoFormat('dddd D [de] MMMM, YYYY'))
            ->description('Lista de visitas a realizar durante el día.')
            ->emptyStateHeading('No hay visitas programadas para hoy')
            ->defaultSort('num_ruta', 'ASC')
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
            ])
            ->filters([])
            ->actions([
                ActionGroup::make([

                    Tables\Actions\Action::make('registrar_visita')
                        ->label('Registrar visita')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->modalHeading('Registrar visita')
                        ->form([
                            Select::make('tipo_visita')
                                ->label('Tipo de Visita')
                                ->options([
                                    'EN' => 'Entrega',
                                    'SE' => 'Seguimiento',
                                    'SV' => 'Siguiente Visita',
                                ])
                                ->required(),

                            Textarea::make('notas')
                                ->label('Observaciones')
                                ->rows(3),

                            FileUpload::make('evidencias')
                                ->label('Evidencia')
                                ->directory('evidencias-visitas'),

                            Section::make('Cobro')
                                ->collapsed()
                                ->description('Si se realiza un cobro, complete la siguiente información.')
                                ->schema([
                                    TextInput::make('monto')
                                        ->label('Monto del cobro')
                                        ->numeric(),

                                    Select::make('tipo_pago')
                                        ->label('Método de pago')
                                        ->options([
                                            'EF' => 'Efectivo',
                                            'TR' => 'Transferencia',
                                            'DP' => 'Depósito bancario',
                                            'CH' => 'Cheque',
                                            'OT' => 'Otro',
                                        ]),

                                    FileUpload::make('comprobantes')
                                        ->label('Comprobantes de pago')
                                        ->directory('comprobantes-cobros')
                                        ->columnSpanFull(),

                                    Textarea::make('comentarios')
                                        ->label('Comentarios adicionales')
                                        ->columnSpanFull()
                                        ->rows(3),
                                ])->columns(2)
                        ])
                        ->action(function (array $data, Pedido $record) {
                            DB::transaction(function () use ($data, $record) {
                                // 1. Crear la visita
                                $visita = $record->visitas()->create([
                                    'fecha_visita' => Carbon::now(),
                                    'notas' => $data['notas'],
                                    'tipo_visita' => $data['tipo_visita'],
                                    'user_id' => Auth::id(),
                                    'evidencias' => $data['evidencias'] ?? null,
                                ]);

                                // 2. Crear el cobro solo si hay monto
                                if (!blank($data['monto'])) {
                                    $record->cobros()->create([
                                        'fecha_pago' => $visita->fecha_visita,
                                        'monto' => $data['monto'],
                                        'tipo_pago' => $data['tipo_pago'],
                                        'comprobantes' => $data['comprobantes'] ?? null,
                                        'comentarios' => $data['comentarios'] ?? null,
                                        'user_id' => Auth::id(),
                                        'visita_id' => $visita->id,
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title('Visita registrada')
                                ->body('La información de la visita fue guardada correctamente.')
                                ->success()
                                ->send();
                        })->visible(function ($record) {
                            return !$record->visitas()
                                ->whereDate('fecha_visita', Carbon::now()->toDateString())
                                ->exists();
                        }),

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
