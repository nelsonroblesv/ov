<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipios extends Model
{
    protected $fillable = ['nombre', 'estados_id'];

    public function estados(): BelongsTo
    {
        return $this->belongsTo(Estados::class);
    }
    
    public function colonias(): HasMany
    {
        return $this->hasMany(Colonias::class);
    }
   
    public function zoneLocations(): HasMany
    {
        return $this->hasMany(ZoneLocation::class);
    }
}
