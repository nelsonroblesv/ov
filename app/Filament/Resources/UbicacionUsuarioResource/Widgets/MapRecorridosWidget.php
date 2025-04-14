<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class MapRecorridosWidget extends Widget
{
    protected static string $view = 'filament.widgets.map-recorridos-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Mapa de Recorridos';

    public static function canView(): bool
    {
        return true;
    }
}
