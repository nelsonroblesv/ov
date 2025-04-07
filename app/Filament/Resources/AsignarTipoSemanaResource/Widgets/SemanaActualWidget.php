<?php

namespace App\Filament\Resources\AsignarTipoSemanaResource\Widgets;

use App\Models\AsignarTipoSemana;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SemanaActualWidget extends BaseWidget
{

    protected static ?int $sort = -1; // opcional para orden

    protected static string $view = 'filament.widgets.semana-actual-widget';

    protected static string $maxWidth = 'xl'; // también puedes probar con 'lg'

    protected int | string | array $columnSpan = '1'; // puede ser 'sm', 'md', 1, 2, etc.

    protected function getStats(): array
    {
        return [
            //
        ];
    }

    public function getSemana(): string
    {
        $tipo = AsignarTipoSemana::first()?->tipo_semana;

        return match ($tipo) {
            0 => 'PAR',
            1 => 'NON',
            default => 'Desconocida',
        };
    }
}   
