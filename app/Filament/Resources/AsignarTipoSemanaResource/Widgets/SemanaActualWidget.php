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

    protected function getStats(): array
    {
        $semana = AsignarTipoSemana::first(); // Asegúrate de que exista al menos un registro

        $tipo = $semana && $semana->tipo_semana == 1 ? 'NON' : 'PAR';
        $periodo = $semana ? $semana->periodo : 'Desconocido';
        $semana = $semana ? $semana->semana : 'Desconocida';

        $color = $tipo === 'PAR' ? 'success' : 'info'; // success: verde, info: azul
        $label = $tipo === 'PAR' ? '🟢 Semana PAR' : '🔵 Semana NON';
        $icon = 'heroicon-o-calendar';

        return [
            Stat::make('Semana Actual', $label)
                ->description('Periodo: ' . $periodo . ' | Semana: ' . $semana)
                ->descriptionIcon($icon)
                ->color($color)
                ->extraAttributes([
                    'class' => 'text-xl font-bold',
                ]),
        ];
    }

    public function getSemana(): array
    {
        $semana = AsignarTipoSemana::first();

        $tipo = $semana && $semana->tipo_semana == 1 ? 'NON' : 'PAR';

        $color = $tipo === 'PAR' ? 'success' : 'info'; // success: verde, info: azul
        $label = $tipo === 'PAR' ? '🟢 Semana PAR' : '🔵 Semana NON';
        $icon = ' <x-filament::icon-button icon="heroicon-m-calendar" />';
        
        return [
            'label' => $label,
            'description' => 'Planifica tus rutas correctamente.',
            'icon' => $icon,
            'color' => $color,
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
