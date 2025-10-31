<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use App\Filament\Resources\PedidosResource\Widgets\StatsOverview;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction as ActionsEditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;


class ListPedidos extends ListRecords
{
    protected static string $resource = PedidosResource::class;
    protected static ?string $title = 'Gestión de Pedidos';
    public array $filtros = [];

    protected function getFiltrosHtml(): ?HtmlString
    {
        $etiquetas = [
            'year' => 'Año',
            'tipo_semana_nota' => 'Tipo Semana',
            'periodo' => 'Periodo',
            'semana' => 'Semana',
            'dia_nota' => 'Día',
            'customer_type' => 'Tipo Cliente',
        ];

        $semana = ['P' => 'Par', 'N' => 'Non'];
        $days = ['L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles', 'J' => 'Jueves', 'V' => 'Viernes'];
        $customerTypes = ['N' => 'Nuevo', 'R' => 'Recurrente'];

        $badges = [];

        foreach ($this->filtros as $campo => $valor) {
            if (!blank($valor)) {
                $etiqueta = $etiquetas[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

                if ($campo === 'tipo_semana_nota' && isset($semana[$valor])) {
                    $valor = $semana[$valor];
                }

                if ($campo === 'dia_nota' && isset($days[$valor])) {
                    $valor = $days[$valor];
                }

                if ($campo === 'customer_type' && isset($customerTypes[$valor])) {
                    $valor = $customerTypes[$valor];
                }

                $badges[] = <<<HTML
                <span style="font-family:Poppins;font-size:11px;padding:3px 9px;border-radius:5px;background:#3a3327;margin-right:5px;color:#e19f1e;border:1px solid #e19f1e;display:inline-flex;align-items:center;gap:5px;">
                    {$etiqueta}: {$valor}
                    <button wire:click="quitarFiltro('{$campo}')" style="background:transparent;border:none;cursor:pointer;">x</button>
                </span>
            HTML;
            }
        }

        // Si no hay badges, retorna null
        if (empty($badges)) {
            return null;
        }

        $html = '<div class="filtros-aplicados">
                <strong style="color:#3cca73; font-family:Poppins; font-size:12px;">
                    Filtros aplicados:
                </strong>
             </div>';

        $html .= '<div class="filtros-aplicados" style="font-size:13px;">' . implode('', $badges) . '</div>';

        return new HtmlString($html);
    }

    public function quitarFiltro(string $campo): void
    {
        unset($this->filtros[$campo]);

        // Limpia el campo pero mantiene los demás filtros
        $this->resetPage();     // Opcional: reinicia paginación
        $this->resetTable();    // fuerza recarga de la tabla
    }


    public function table(Table $table): Table
    {
        return $table
            ->heading('Lista de pedidos')
            ->description($this->getFiltrosHtml() ?? '')
            ->reorderable('num_ruta')
            ->defaultSort('created_at', 'ASC')
            ->recordUrl(
                fn (Model $record): string => PedidosResource::getUrl('view', ['record' => $record]),
                )
            ->columns([
                TextColumn::make('num_ruta')
                    ->label('# Ruta')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('real_id')
                    ->label('Cliente Real')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->estado_pedido === 'cambio') {
                            return optional(Customer::find($state))->name;
                        }

                        if ($record->estado_pedido === 'susana') {
                            return optional(User::find($state))->name;
                        }

                        return '—';
                    }),

                TextColumn::make('userDistribuidor.name')
                    ->label('Distribuidor')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-user'),

                TextColumn::make('userReparto.name')
                    ->label('Reparto')
                    ->searchable()
                    ->sortable()->badge()
                    ->color('info')
                    ->icon('heroicon-m-truck'),

                TextColumn::make('fecha_entrega')
                    ->label('Fecha Entrega')
                    ->searchable()
                    ->sortable()
                    ->date(),

                TextColumn::make('fecha_liquidacion')
                    ->label('Fecha de Liquidación')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if (! $state) {
                            return '-';
                        }

                        $fechaLiquidacion = Carbon::parse($record->fecha_liquidacion);
                        $diasTranscurridos = intval($fechaLiquidacion->diffInDays(Carbon::now()));

                        return $fechaLiquidacion->translatedFormat('M d, Y') . " ({$diasTranscurridos} días)";
                    })
                    ->color(function ($record) {
                        if (! $record->fecha_liquidacion) {
                            return 'gray';
                        }

                        $fechaLiquidacion = Carbon::parse($record->fecha_liquidacion);
                        $diasTranscurridos = $fechaLiquidacion->diffInDays(Carbon::now());

                        return match (true) {
                            $diasTranscurridos <= 30 => 'success',
                            $diasTranscurridos <= 45 => 'warning',
                            default => 'danger',
                        };
                    })
                    ->sortable(),

                TextColumn::make('monto')
                    ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2)),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->badge()
                    ->color(function ($record) {
                        $totalCobrosAprobados = $record->cobros()
                            ->where('aprobado', true)
                            ->sum('monto');

                        $saldo = $record->monto - $totalCobrosAprobados;

                        return match (true) {
                            $saldo === 0.0     => 'success', // verde
                            $saldo < 0         => 'info',    // azul (excedente)
                            $saldo > 0         => 'danger',  // rojo
                        };
                    })
                    ->getStateUsing(function ($record) {
                        $totalCobrosAprobados = $record->cobros()
                            ->where('aprobado', true)
                            ->sum('monto');

                        $saldo = $record->monto - $totalCobrosAprobados;

                        return '$ ' . number_format($saldo, 2);
                    }),

                TextColumn::make('tipo_nota')
                    ->label('Tipo Nota')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'sistema' => 'SISTEMA',
                        'real' => 'REAL',
                        'stock' => 'DE STOCK'
                    ][$state] ?? 'Otro')
                /*  ->badge()
                    ->color(fn(string $state): string => [
                        'sistema' => 'success',
                        'real' => 'info',
                        'stock' => 'warning'
                    ][$state] ?? 'primary')*/,

                TextColumn::make('estado_pedido')
                    ->label('Estado Pedido')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'cambio' => 'CAMBIO',
                        'cancelado' => 'CANCELADO',
                        'entrega' => 'ENTREGA',
                        'pagado' => 'PAGADO',
                        'pendiente' => 'PENDIENTE',
                        'reposicion' => 'REPOSICIÓN',
                        'susana' => 'SUSANA'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color(fn(string $state): string => [
                        'cambio' => 'info',
                        'cancelado' => 'danger',
                        'entrega' => 'warning',
                        'pagado' => 'success',
                        'pendiente' => 'primary',
                        'reposicion' => 'info',
                        'susana' => 'light'
                    ][$state] ?? 'primary'),

                TextColumn::make('tipo_semana_nota')
                    ->label('PAR/NON')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'P' => 'PAR',
                        'N' => 'NON'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color(fn(string $state): string => [
                        'P' => 'success',
                        'N' => 'info'
                    ][$state] ?? 'primary'),

                TextColumn::make('dia_nota')
                    ->label('Día Nota')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'L' => 'LUNES',
                        'M' => 'MARTES',
                        'X' => 'MIÉRCOLES',
                        'J' => 'JUEVES',
                        'V' => 'VIERNES'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('customer_type')
                    ->label('Tipo Cliente')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'N' => 'NUEVO',
                        'R' => 'RECURRENTE'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color(fn(string $state): string => [
                        'N' => 'success',
                        'R' => 'info'
                    ][$state] ?? 'primary'),

                TextColumn::make('zona.nombre_zona')
                    ->label('Zona')
                    ->searchable()
                    ->sortable()
                    ->color('info'),

                TextColumn::make('periodo')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('semana')
                    ->badge()
                    ->color('danger')
                    ->alignCenter(),

                IconColumn::make('factura')
                    ->label('Facturada')
                    ->searchable()
                    ->boolean()
                    ->sortable(),

                TextColumn::make('estado_general')
                    ->label('Estado General')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->searchable()
                    ->sortable()
                    ->date(),

                TextColumn::make('id_nota')
                    ->label('ID Nota')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('num_pedido')
                    ->label('# Pedido')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('region.name')
                    ->label('Región')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->actions([

                Action::make('ver_nota')
                    ->label('Nota de Venta')
                    ->icon('heroicon-m-document-text')
                    ->color('success')
                    ->url(fn($record) => PedidosResource::getUrl('nota-venta', ['record' => $record]))
                    ->openUrlInNewTab(),

                ActionsEditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),

                Action::make('facturar')
                    ->label('Facturar')
                    ->icon('heroicon-m-document-text')
                    ->color('success'),

            ], position: ActionsPosition::BeforeColumns)
            
            ->headerActions([
                Action::make('filtrar')
                    ->label('Filtros avanzados')
                    ->icon('heroicon-m-funnel')
                    ->color('warning')
                    ->form([
                        Section::make('Selecciona los filtros a aplicar')->schema([
                            Select::make('year')
                                ->label('Año')
                                ->placeholder('Todos')
                                ->options([
                                    '2024' => '2024',
                                    '2025' => '2025',
                                ])
                                ->default($this->filtros['year'] ?? null),

                            Select::make('tipo_semana_nota')
                                ->label('Tipo de semana')
                                ->placeholder('Todos')
                                ->options([
                                    'P' => 'PAR',
                                    'N' => 'NON',
                                ])
                                ->default($this->filtros['tipo_semana_nota'] ?? null),

                            Select::make('periodo')
                                ->label('Periodo')
                                ->placeholder('Todos')
                                ->options([
                                    '1' => 'P01',
                                    '2' => 'P02',
                                    '3' => 'P03',
                                    '4' => 'P04',
                                    '5' => 'P05',
                                    '6' => 'P06',
                                    '7' => 'P07',
                                    '8' => 'P08',
                                    '9' => 'P09',
                                    '10' => 'P10',
                                    '11' => 'P11',
                                    '12' => 'P12',
                                    '13' => 'P13',
                                ])
                                ->default($this->filtros['periodo'] ?? null),

                            Select::make('semana')
                                ->label('Semana')
                                ->placeholder('Todos')
                                ->options([
                                    '1' => 'S1',
                                    '2' => 'S2',
                                    '3' => 'S3',
                                    '4' => 'S4'
                                ])
                                ->default($this->filtros['semana'] ?? null),

                            Select::make('dia_nota')
                                ->label('Día Nota')
                                ->placeholder('Todos')
                                ->options([
                                    'L' => 'Lunes',
                                    'M' => 'Martes',
                                    'X' => 'Miércoles',
                                    'J' => 'Jueves',
                                    'V' => 'Viernes'
                                ])
                                ->default($this->filtros['dia_nota'] ?? null),

                            Select::make('customer_type')
                                ->label('Tipo Cliente')
                                ->placeholder('Todos')
                                ->options([
                                    'N' => 'Nuevo',
                                    'R' => 'Recurrente'
                                ])
                                ->default($this->filtros['customer_type'] ?? null),
                        ])->columns(3)
                    ])
                    ->action(function (array $data): void {
                        $this->filtros = $data ?? [];
                    })
                    ->modalHeading('Filtros')
                    ->modalSubmitActionLabel('Aplicar'),

                Action::make('limpiarFiltros')
                    ->label('Limpiar filtros')
                    ->icon('heroicon-m-x-circle')
                    ->color('gray')
                    ->action(function () {
                        $this->filtros = [];
                        $this->resetTable();
                    })
            ])
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->when($this->filtros['year'] ?? null, fn($q, $valor) => $q->where('year', $valor))
                    ->when($this->filtros['tipo_semana_nota'] ?? null, fn($q, $valor) => $q->where('tipo_semana_nota', $valor))
                    ->when($this->filtros['periodo'] ?? null, fn($q, $valor) => $q->where('periodo', $valor))
                    ->when($this->filtros['semana'] ?? null, fn($q, $valor) => $q->where('semana', $valor))
                    ->when($this->filtros['dia_nota'] ?? null, fn($q, $valor) => $q->where('dia_nota', $valor))
                    ->when($this->filtros['customer_type'] ?? null, fn($q, $valor) => $q->where('customer_type', $valor));
            })
            ->filters([
                SelectFilter::make('estado_pedido')
                    ->label('Estado Pedido')
                    ->placeholder('Todos')
                    ->options([
                        'cambio' => 'CAMBIO',
                        'cancelado' => 'CANCELADO',
                        'entrega' => 'ENTREGA',
                        'pagado' => 'PAGADO',
                        'pendiente' => 'PENDIENTE',
                        'reposicion' => 'REPOSICIÓN',
                        'susana' => 'SUSANA'
                    ]),

                SelectFilter::make('month')
                    ->label('Mes')
                    ->placeholder('Todos')
                    ->options([
                        '1' => 'Enero',
                        '2' => 'Febrero',
                        '3' => 'Marzo',
                        '4' => 'Abril',
                        '5' => 'Mayo',
                        '6' => 'Junio',
                        '7' => 'Julio',
                        '8' => 'Agosto',
                        '9' => 'Septiembre',
                        '10' => 'Octubre',
                        '11' => 'Noviembre',
                        '12' => 'Diciembre'
                    ]),

                SelectFilter::make('distribuidor')
                    ->label('Distribuidor')
                    ->placeholder('Todos')
                    ->options(User::query()
                        ->where('is_active', true)
                        ->whereIn('role', ['Vendedor'])
                        ->orderBy('name', 'ASC')
                        ->pluck('name', 'id')),

                SelectFilter::make('reparto')
                    ->label('Reparto')
                    ->placeholder('Todos')
                    ->options(User::query()
                        ->where('is_active', true)
                        ->whereIn('role', ['Vendedor', 'Repartidor'])
                        ->orderBy('name', 'ASC')
                        ->pluck('name', 'id')),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Pedido')
                ->icon('heroicon-o-shopping-bag'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class
        ];
    }

}
