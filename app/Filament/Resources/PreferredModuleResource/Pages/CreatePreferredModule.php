<?php

namespace App\Filament\Resources\PreferredModuleResource\Pages;

use App\Filament\Resources\PreferredModuleResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\IconPosition;

class CreatePreferredModule extends CreateRecord
{
    protected static string $resource = PreferredModuleResource::class;
    protected static ?string $title = 'Nuevo Modulo Preferred';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Modulo registrado')
            ->body('Se ha registrado un nuevo Modulo, ahora ya puedes agregar productos.')
            ->icon('heroicon-o-check')
            ->iconColor('info')
            ->color('info');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Registrar Modulo')
            ->icon('heroicon-o-shopping-bag')
            ->iconPosition(IconPosition::Before)
            ->color('success');;
    }

    protected function getCreateAnotherFormAction(): Action
   {
       return parent::getCreateAnotherFormAction()
           //->label('Save & Create Another')
           //->icon('heroicon-o-plus-circle')
           //->iconPosition(IconPosition::Before);
           ->hidden();
   }

   protected function getCancelFormAction(): Action
   {
       return parent::getCancelFormAction()
           ->label('Regresar')
           ->icon('heroicon-o-arrow-uturn-left')
           ->color('gray');
   }

   protected function mutateFormDataBeforeCreate(array $data): array
   {
    $recipient = auth()->user();

    Notification::make()
        ->title('Nuevo Modulo')
        ->body("**Se ha registrado un nuevo Modulo Preferred**")
        ->sendToDatabase($recipient);
        return $data;
   }
}
