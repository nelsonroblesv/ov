<?php

namespace App\Filament\Resources\MarcaResource\Pages;

use App\Filament\Resources\MarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMarca extends ViewRecord
{
    protected static string $resource = MarcaResource::class;
    protected static ?string $title = 'Vista de Marca';
}
