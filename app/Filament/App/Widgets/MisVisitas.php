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

                TextColumn::make('customer.zona.nombre_zona')
                    ->label('Ubicación')
                    ->color('info')
                    ->description(fn($record) => $record->customer?->regiones?->name ?? 'Sin información'),

                TextColumn::make('tipo_visita')
                    ->label('Visita')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $customer = $record->customer?->name ?? 'Sin información';
                        $phone = $record->customer?->phone ?? 'Sin información';
                        $email = $record->customer?->email ?? 'Sin información';
                        return "
                            <span style='font-weight:bold;'>{$customer}</span><br>
                            <span>{$phone}</span><br>
                            <span>{$email}</span>";
                    })
            ])
            ->filters([

            ])
            ->actions([
                 Tables\Actions\Action::make('view_invoice')
                    ->label('EC')
                    ->icon('heroicon-o-document-chart-bar')
                    ->url(fn($record) => CustomerStatementResource::getUrl(name: 'invoice', parameters: ['record' => $record->customer]))
                    ->openUrlInNewTab()
            ]);
    }
}
