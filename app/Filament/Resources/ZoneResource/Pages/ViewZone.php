<?php

namespace App\Filament\Resources\ZoneResource\Pages;

use App\Filament\Resources\ZoneResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewZone extends ViewRecord
{
    protected static string $resource = ZoneResource::class;
    protected static ?string $title = 'Vista de Zona';
}
