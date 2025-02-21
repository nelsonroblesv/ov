<?php

namespace App\Filament\App\Resources\ItinerarioResource\Pages;

use App\Filament\App\Resources\ItinerarioResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItinerarios extends ListRecords
{

    protected static string $resource = ItinerarioResource::class;
    protected static ?string $title = 'Itinerarios';
}