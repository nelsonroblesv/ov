<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zonas extends Model
{
    protected $fillable = ['regiones_id', 'nombre_zona', 'color_zona', 'dia_zona'];

    public function regiones(): BelongsTo
    {
        return $this->belongsTo(Regiones::class, 'regiones_id'); 
    }
}
