<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\CustomerStatementResource;
use App\Filament\Resources\CustomerResource;
use Filament\Tables;
use App\Models\EntregaCobranzaDetalle;
use Carbon\Carbon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

Carbon::setLocale('es');

class MisVisitas extends BaseWidget
{

    //protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Visitas programadas: ' . Carbon::now()->isoFormat('dddd D [de] MMMM, YYYY'))
            ->description('Lista de visitas para el día de hoy y anteriores no realizadas.')
            ->query(
                EntregaCobranzaDetalle::query()
                    ->where('user_id', Auth::id())
                    ->whereDate('fecha_programada', '<=', Carbon::now())
                    ->where('status', false)
                    ->with('customer', 'entregaCobranza')
            )
            ->columns([

                TextColumn::make('tipo_visita')
                    ->label('Ubicaciones')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $region = $record->customer?->regiones?->name ?? 'Sin región';
                        $zona = $record->customer?->zona?->nombre_zona ?? 'Sin zona';
                        $tipo = $record->tipo_visita ?? 'Sin tipo';

                        $tipoInfo = match ($tipo) {
                            'PR' => ['label' => 'Prospecto'],
                            'PO' => ['label' => 'Posible'],
                            'EP' => ['label' => 'Entrega Primer Pedido'],
                            'ER' => ['label' => 'Entrega Recurrente'],
                            'CO' => ['label' => 'Cobranza'],
                            default => ['label' => 'Otro'],
                        };

                        return "<span>📍 {$region}</span><br>
                                <span>📌 {$zona}<br>
                                <span>✅ {$tipoInfo['label']}</span>";
                    }),

                    TextColumn::make('customer_id')
                    ->label('Detalles')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $customer = $record->customer?->name ?? 'Sin región';
                        $phone = $record->customer?->phone ?? 'Sin zona';
                        $email = $record->customer?->email ?? 'Sin zona';

                        return "<span>{$customer}</span><br>
                                <span><a href='tel:'{$phone}>{$phone}</a><br>
                                <span><a href='mailto:'{$email}>{$email}</a>";
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('view_invoice')
                    ->label('')
                    ->icon('heroicon-o-document-chart-bar')
                    ->url(fn($record) => CustomerStatementResource::getUrl(name: 'invoice', parameters: ['record' => $record->customer]))
                    ->openUrlInNewTab()
            ]);
    }
}
