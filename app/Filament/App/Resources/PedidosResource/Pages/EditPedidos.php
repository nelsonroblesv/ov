<?php

namespace App\Filament\App\Resources\PedidosResource\Pages;

use App\Filament\App\Resources\PedidosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPedidos extends EditRecord
{
    protected static string $resource = PedidosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
