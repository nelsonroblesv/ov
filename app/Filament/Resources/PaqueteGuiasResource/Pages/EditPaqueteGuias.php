<?php

namespace App\Filament\Resources\PaqueteGuiasResource\Pages;

use App\Filament\Resources\PaqueteGuiasResource;
use Filament\Actions;
<<<<<<< HEAD
<<<<<<< HEAD
use Filament\Notifications\Notification;
=======
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
=======
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
use Filament\Resources\Pages\EditRecord;

class EditPaqueteGuias extends EditRecord
{
    protected static string $resource = PaqueteGuiasResource::class;
<<<<<<< HEAD
<<<<<<< HEAD
    protected static ?string $title = 'Editar Paquete de Guías';

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
=======
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
=======
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0

    protected function getHeaderActions(): array
    {
        return [
<<<<<<< HEAD
<<<<<<< HEAD
            Actions\DeleteAction::make()
                ->label('Borrar'),
        ];
    }

}
=======
=======
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
            Actions\DeleteAction::make(),
        ];
    }
}
<<<<<<< HEAD
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
=======
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
