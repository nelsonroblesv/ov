<?php

namespace App\Filament\Resources\PaqueteGuiasResource\Pages;

use App\Filament\Resources\PaqueteGuiasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaqueteGuias extends ListRecords
{
    protected static string $resource = PaqueteGuiasResource::class;
<<<<<<< HEAD
    protected static ?string $title = 'Paquetes de Guías';
=======
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
<<<<<<< HEAD
            ->label('Registrar Paquete de Guías')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('success')
        ];
    }
}
=======
            ->label('Registrar Paquete de Guias')
            ->icon('heroicon-o-archive-box')
        ];
    }
}
>>>>>>> 0df0e650220aea14651197eb698b625ff483f8b0
