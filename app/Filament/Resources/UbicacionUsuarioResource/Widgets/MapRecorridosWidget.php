<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class MapRecorridosWidget extends Widget
{
    protected static string $view = 'filament.widgets.map-recorridos-widget';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    protected static bool $isLazy = true; // solo se carga una vez
    public function getPollingInterval(): ?string
    {
        return null; // sin actualizaciones en vivo
    }
}
