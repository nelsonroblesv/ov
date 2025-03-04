<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zonas extends Model
{
    protected $fillable = ['regiones_id', 'nombre_zona', 'tipo_semana', 'dia_zona', 'user_id'];

    public function regiones(): BelongsTo
    {
        return $this->belongsTo(Regiones::class, 'regiones_id'); 
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
    public function rutas(): HasMany
    {
        return $this->hasMany(Rutas::class);
    }
}
