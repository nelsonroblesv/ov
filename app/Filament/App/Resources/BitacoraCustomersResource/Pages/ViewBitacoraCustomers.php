<?php

namespace App\Filament\App\Resources\BitacoraCustomersResource\Pages;

use App\Filament\App\Resources\BitacoraCustomersResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBitacoraCustomers extends ViewRecord
{
    protected static string $resource = BitacoraCustomersResource::class;
    protected static ?string $title = 'Vista Registro de Bitacora';
}
