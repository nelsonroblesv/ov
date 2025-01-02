<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoneLocation extends Model
{
    protected $fillable = ['zone_id', 'municipios_id'];

    // Relación con Zone: Una ubicación pertenece a una zona
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function paises(): BelongsTo
    {
        return $this->belongsTo(Paises::class);
    }

    public function estados(): BelongsTo
    {
        return $this->belongsTo(Estados::class);
    }

    public function municipios(): BelongsTo
    {
        return $this->belongsTo(Municipios::class);
    }
    
    public function colonias(): BelongsTo
    {
        return $this->belongsTo(Colonias::class);
    }
}
