<?php

namespace App\Filament\Resources\CobranzaResource\Pages;

use App\Filament\Resources\CobranzaResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCobranza extends CreateRecord
{
    protected static string $resource = CobranzaResource::class;
    protected static ?string $title = 'Registrar Saldo Deudor';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Deuda registrada')
            ->body('Se ha registrado una nueva Deuda de forma exitosa.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_at'] = Carbon::now()->setTimezone('America/Merida')->format('Y-m-d H:i:s');
        $data['updated_at'] = null;
        return $data;
    }


}
