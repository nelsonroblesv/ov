<?php

namespace App\Filament\Resources\AsignarTipoSemanaResource\Widgets;

use App\Models\AsignarTipoSemana;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SemanaActualWidget extends BaseWidget
{

    protected static ?int $sort = -3; // opcional para orden
    protected static string $view = 'filament.widgets.semana-actual-widget';
    protected static string $maxWidth = 'xl'; // también puedes probar con 'lg'
    protected int | string | array $columnSpan = '1'; // puede ser 'sm', 'md', 1, 2, etc.


    public function getSemana(): array
    {
        $semana = AsignarTipoSemana::first();

        $tipo = $semana && $semana->tipo_semana == 1 ? 'NON' : 'PAR';
        $periodo = $semana ? $semana->periodo : 'Desconocido';
        $semanaActual = $semana ? $semana->semana : 'Desconocida';

        $color = $tipo === 'PAR' ? 'success' : 'info'; // success: verde, info: azul
        $label = $tipo === 'PAR' ? '🟢 Semana PAR' : '🔵 Semana NON';
        $icon = ' <x-filament::icon-button icon="heroicon-m-calendar" />';
        
        return [
            'label' => $label,
            'periodo' => $periodo,
            'semanaActual' => $semanaActual,
            'extraAttributes' => [
                'class' => 'text-xl font-bold',
            ],
        ];
    }

    public function getViewData(): array
    {
        return $this->getSemana();
    }
}   
