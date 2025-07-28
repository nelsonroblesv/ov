<?php

namespace App\Filament\App\Resources\VisitasResource\Pages;

use App\Filament\App\Resources\VisitasResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVisitas extends CreateRecord
{
    protected static string $resource = VisitasResource::class;
}
