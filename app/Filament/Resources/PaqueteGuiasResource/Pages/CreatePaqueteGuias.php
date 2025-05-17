<?php

namespace App\Filament\Resources\PaqueteGuiasResource\Pages;

use App\Filament\Resources\PaqueteGuiasResource;
<<<<<<< HEAD
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
=======
use Filament\Actions;
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
use Filament\Resources\Pages\CreateRecord;

class CreatePaqueteGuias extends CreateRecord
{
    protected static string $resource = PaqueteGuiasResource::class;
<<<<<<< HEAD
    protected static ?string $title = 'Nuevo Paquete de Guías';

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
}
=======
}
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
