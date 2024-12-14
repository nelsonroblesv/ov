<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource as ResourcesCategoryResource;
use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = ResourcesCategoryResource::class;
    protected static ?string $title = 'Nueva Familia de Productos';
}
