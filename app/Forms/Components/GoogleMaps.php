<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class GoogleMaps extends Field
{
    protected string $view = 'forms.components.google-maps';

    public static function make(string $name): static
    {
        return parent::make($name);
    }
}
