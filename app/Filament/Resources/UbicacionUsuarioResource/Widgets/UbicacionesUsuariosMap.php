<?php

namespace App\Filament\Resources\UbicacionUsuarioResource\Widgets;

use App\Models\UbicacionUsuario;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UbicacionesUsuariosMap extends BaseWidget
{
    protected function getStats(): array
    {
        return UbicacionUsuario::with('user')
            ->latest()
            ->limit(100) // Opcional para no saturar
            ->get()
            ->map(function ($ubicacion) {
                return [
                    'location' => [
                        'lat' => $ubicacion->latitud,
                        'lng' => $ubicacion->longitud,
                    ],
                    'title' => $ubicacion->user->name ?? 'Usuario',
                    'infoWindowContent' => view('components.map-info-window', [
                        'usuario' => $ubicacion->user->name,
                        'fecha' => $ubicacion->created_at->format('d/m/Y H:i'),
                    ])->render(),
                ];
            })
            ->toArray();
    }

    protected function getZoom(): int
    {
        return 6;
    }

    protected function getMapHeight(): string
    {
        return '500px';
    }
}
