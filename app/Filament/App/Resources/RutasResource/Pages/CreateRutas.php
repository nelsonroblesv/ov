<?php

namespace App\Filament\App\Resources\RutasResource\Pages;

use App\Filament\App\Resources\RutasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRutas extends CreateRecord
{
    protected static string $resource = RutasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
       /// $data['name'] = ucwords(strtolower($data['name']));
        return $data;
    }
}
