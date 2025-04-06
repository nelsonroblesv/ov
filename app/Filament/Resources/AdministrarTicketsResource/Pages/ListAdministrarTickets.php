<?php

namespace App\Filament\Resources\AdministrarTicketsResource\Pages;

use App\Filament\App\Resources\TicketsResource;
use App\Filament\Resources\AdministrarTicketsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdministrarTickets extends ListRecords
{
    protected static string $resource = AdministrarTicketsResource::class;
    protected static ?string $title = 'Tickets';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Ticket')
                ->icon('heroicon-o-ticket'),
        ];
    }
}
