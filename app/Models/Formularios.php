<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Formularios extends Model
{
    protected $fillable = [
       'eventos_id', 'fecha_registro', 'nombre', 'ciudad', 'email', 'telefono'
    ];

    public function eventos(): BelongsTo
    {
        return $this->belongsTo(Eventos::class);
    }
}
