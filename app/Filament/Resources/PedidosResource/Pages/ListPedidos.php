<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use App\Filament\Resources\PedidosResource\Widgets\StatsOverview;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidosResource::class;
    protected static ?string $title = 'Historial de Pedidos';
    public array $filtros = [];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Lista de Pedidos')
            ->description('Pedidos registrados en el sistema')
            ->columns([
                TextColumn::make('num_ruta')
                    ->label('# Ruta')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('userDistribuidor.name')
                    ->label('Distribuidor')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-building-library'),

                TextColumn::make('userReparto.name')
                    ->label('Reparto')
                    ->searchable()
                    ->sortable()->badge()
                    ->color('info')
                    ->icon('heroicon-m-archive-box-arrow-down'),

                TextColumn::make('fecha_entrega')
                    ->label('Fecha Entrega')
                    ->searchable()
                    ->sortable()
                    ->date(),

                TextColumn::make('fecha_liquidacion')
                    ->label('Fecha Liquidación')
                    ->searchable()
                    ->sortable()
                    ->date(),

                TextColumn::make('monto')
                    ->formatStateUsing(fn(string $state) => '$ ' . number_format($state, 2)),

                TextColumn::make('saldo'),

                TextColumn::make('tipo_nota')
                    ->label('Tipo Nota')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => [
                        'sistema' => 'SISTEMA',
                        'real' => 'REAL',
                        'stock' => 'DE STOCK'
                    ][$state] ?? 'Otro')
                    ->formatStateUsing(fn(string $state): string => [
                        'sistema' => 'SISTEMA',
                        'real' => 'REAL',
                        'stock' => 'STOCK'
                    ][$state] ?? 'Otro')
                    ->badge()
                    ->color(fn(string $state): string => [
                        'sistema' => 'success',
                        'real' => 'info',
                        'stock' => 'warning'
                    ][$state] ?? 'primary'),

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
                    ->label('Factura')
                    ->searchable()
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha Creación')
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
            ->headerActions([

                Action::make('filtrar')
                    ->label('Filtros')
                    ->icon('heroicon-m-funnel')
                    ->form([
                       Section::make('Selecciona los filtros a aplicar')->schema([
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

                        Select::make('distribuidor')
                            ->label('Distribuidor')
                            ->placeholder('Todos')
                            ->options(User::query()
                                ->where('is_active', true)
                                ->whereIn('role', ['Vendedor'])
                                ->orderBy('name', 'ASC')
                                ->pluck('name', 'id'))
                            ->default($this->filtros['distribuidor'] ?? null),

                        Select::make('reparto')
                            ->label('Reparto')
                            ->placeholder('Todos')
                            ->options(User::query()
                                ->where('is_active', true)
                                ->whereIn('role', ['Vendedor', 'Repartidor'])
                                ->orderBy('name', 'ASC')
                                ->pluck('name', 'id'))
                            ->default($this->filtros['reparto'] ?? null),

                        Select::make('year')
                            ->label('Año')
                            ->placeholder('Todos')
                            ->options([
                                '2024' => '2024',
                                '2025' => '2025',
                            ])
                            ->default($this->filtros['year'] ?? null),

                        Select::make('month')
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
                            ]) 
                            ->default($this->filtros['month'] ?? null),
                       ])->columns(3)
                    ])
                    ->action(function (array $data): void {
                        $this->filtros = $data;
                    })
                    ->modalHeading('Filtros')
                    ->modalSubmitActionLabel('Aplicar')
                   ,

                Action::make('limpiarFiltros')
                    ->label('Limpiar filtros')
                    ->icon('heroicon-m-x-circle')
                    ->color('gray')
                    ->action(fn() => $this->filtros = [])
            ])
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->when($this->filtros['tipo_semana_nota'] ?? null, fn($q, $valor) => $q->where('tipo_semana_nota', $valor))
                    ->when($this->filtros['periodo'] ?? null, fn($q, $valor) => $q->where('periodo', $valor))
                    ->when($this->filtros['semana'] ?? null, fn($q, $valor) => $q->where('semana', $valor))
                    ->when($this->filtros['dia_nota'] ?? null, fn($q, $valor) => $q->where('dia_nota', $valor))
                    ->when($this->filtros['customer_type'] ?? null, fn($q, $valor) => $q->where('customer_type', $valor))
                    ->when($this->filtros['distribuidor'] ?? null, fn($q, $valor) => $q->where('distribuidor', $valor))
                    ->when($this->filtros['reparto'] ?? null, fn($q, $valor) => $q->where('reparto', $valor))
                    ->when($this->filtros['year'] ?? null, fn($q, $valor) => $q->where('year', $valor))
                    ->when($this->filtros['month'] ?? null, fn($q, $valor) => $q->where('month', $valor));
            });
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
