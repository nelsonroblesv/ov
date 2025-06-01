<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\ChartWidget;

class CobranzaSemanal extends ChartWidget
{
    protected static ?string $heading = 'Cobranza semanal';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Cobranza semanal',
                    'data' => [800, 150],
                    'backgroundColor' => [
                            'rgb(61, 200, 122)',
                            'rgb(255, 99, 132)',
                    ],
                    'borderColor' => '#fff',
                ],
            ],
            'labels' => ['Completado', 'Faltante'],
        ];
    }

    protected function getOptions(): ?array
    {
        return [
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false, 
                    ],
                    'ticks' => [
                        'display' => false
                    ]
                ],
                'y' => [
                    'grid' => [
                        'display' => false
                    ],
                    'ticks' => [
                        'display' => false
                    ]
                ]
                
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
