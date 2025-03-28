<?php

namespace App\Filament\Resources\PaqueteGuiaResource\Pages;

use App\Filament\Resources\PaqueteGuiaResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaqueteGuia extends CreateRecord
{
    protected static string $resource = PaqueteGuiaResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_at'] = Carbon::now()->setTimezone('America/Merida')->format('Y-m-d H:i:s');
        return $data;
    }
}
