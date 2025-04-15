<?php

namespace App\Filament\Resources\CobranzaResource\Pages;

use App\Filament\Resources\CobranzaResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCobranza extends EditRecord
{
    protected static string $resource = CobranzaResource::class;
    protected static ?string $title = 'Editar Saldo Deudor';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['updated_at'] = Carbon::now()->setTimezone('America/Merida')->format('Y-m-d H:i:s');
        return $data;
    }
}
