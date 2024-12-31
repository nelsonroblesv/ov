<?php

namespace App\Filament\Resources\MunicipiosResource\Pages;

use App\Filament\Resources\MunicipiosResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMunicipios extends EditRecord
{
    protected static string $resource = MunicipiosResource::class;

    protected static ?string $title = 'Editar Municipio';

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
                ->label('Borrar'),
        ];
    }

}
