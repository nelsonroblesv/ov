<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected static ?string $title = 'Editar Cliente';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Cambios realizados')
            ->body('Se ha actualizado la información del Cliente de forma exitosa.')
            ->icon('heroicon-o-user-plus')
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_at'] = Carbon::now()->setTimezone('America/Merida');
        return $data;
    }
    /*
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Formatea el nombre al cargar el formulario
        $data['name'] = ucwords(strtolower($data['name']));

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Formatea el nombre antes de guardar
        $data['name'] = ucwords(strtolower($data['name']));

        return $data;
    }
        */
}
