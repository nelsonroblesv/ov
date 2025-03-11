<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eventos extends Model
{
    protected $fillable = ['tipo', 'nombre', 'color'];

    public function registros(): HasMany
    {
        return $this->hasMany(Formularios::class);
    }
}
