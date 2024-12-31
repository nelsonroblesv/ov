<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['name', 'color', 'type', 'estados_id'];

    public function estados(): BelongsTo
    {
        return $this->belongsTo(Estados::class);
    }
   
    public function zoneLocations(): HasMany
    {
        return $this->hasMany(ZoneLocation::class);
    }

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

    public function zoneUser(): HasMany
    {
        return $this->hasMany(ZoneUser::class);
    }

}
