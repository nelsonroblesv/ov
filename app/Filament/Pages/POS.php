<?php

namespace App\Filament\Pages;
use Filament\Pages\Page;

class POS extends Page
{
    // Define la vista de Blade que esta página usará
    protected static string $view = 'filament.pages.p-o-s'; 

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $title = 'Registro de Pedidos';

    // Opcional: Deshabilita el layout de Livewire si estás usando un layout Blade personalizado
    // protected bool $usesForm = false;
    
    // ... cualquier otra configuración de la página

    // Asegúrate de que este método devuelva la vista de Blade correcta
    public function getView(): string
    {
        return static::$view;
    }
}