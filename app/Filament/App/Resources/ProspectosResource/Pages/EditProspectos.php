<?php

namespace App\Filament\App\Resources\ProspectosResource\Pages;

use App\Filament\App\Resources\ProspectosResource;
use Carbon\Carbon;
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
            ->title('Cambios realizados')
            ->body('Se ha actualizado el registro correctamente.')
            ->icon('heroicon-o-arrow-path')
            ->iconColor('success')
            ->color('success');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
               // ->label('Borrar'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_at'] = Carbon::now()->setTimezone('America/Merida');
        return $data;
    }
}
