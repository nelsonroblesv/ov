<?php

namespace App\Models;

use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipality extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'state_id'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
    // Relación con ZoneLocation: Un municipio puede estar en muchas zonas (a través de ZoneLocation)
    public function zoneLocations()
    {
        return $this->hasMany(ZoneLocation::class);
    }
}
