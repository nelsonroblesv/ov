<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estados extends Model
{
    protected $fillable = ['nombre', 'paises_id'];

    public function paises(): BelongsTo
    {
        return $this->belongsTo(Paises::class);
    }

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipios::class);
    }
    
    // Relación con Zone: Un estado tiene muchas zonas
    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }
}
