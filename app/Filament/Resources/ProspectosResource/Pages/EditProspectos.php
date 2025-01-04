<?php

namespace App\Filament\Resources\ProspectosResource\Pages;

use App\Filament\Resources\ProspectosResource;
use Cheesegrits\FilamentGoogleMaps\Concerns\InteractsWithMaps;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProspectos extends EditRecord
{
    protected static string $resource = ProspectosResource::class;
    use InteractsWithMaps;

    protected static ?string $title = 'Editar Prospecto';

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