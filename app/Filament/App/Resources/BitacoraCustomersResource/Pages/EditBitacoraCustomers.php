<?php

namespace App\Filament\App\Resources\BitacoraCustomersResource\Pages;

use App\Filament\App\Resources\BitacoraCustomersResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBitacoraCustomers extends EditRecord
{
    protected static string $resource = BitacoraCustomersResource::class;
    protected static ?string $title = 'Editar Registro de Bitácora';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cambios realizados')
            ->body('Se ha actualizado el registro correctamente.')
            ->icon('heroicon-o-check')
            ->iconColor('success')
            ->color('success');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
              //  ->label('Borrar'),
        ];
    }

}
