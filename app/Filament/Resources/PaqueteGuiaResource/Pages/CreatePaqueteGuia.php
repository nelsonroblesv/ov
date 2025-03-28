<?php

namespace App\Filament\Resources\PaqueteGuiaResource\Pages;

use App\Filament\Resources\PaqueteGuiaResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePaqueteGuia extends CreateRecord
{
    protected static string $resource = PaqueteGuiaResource::class;
    protected static ?string $title = 'Nuevo Paquete de Guías';
/*
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
*/
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Paquete de Guías Creado')
            ->body('Se ha registrado un nuevo Paquete de Guías. Ya puedes asignar guías a este paquete.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_at'] = Carbon::now()->setTimezone('America/Merida')->format('Y-m-d H:i:s');
        return $data;
    }
}
