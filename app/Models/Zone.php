<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    //protected $fillable = ['name', 'color', 'estados_id'];

    /*  Update Select Multiple*/ 

    protected $fillable = ['name', 'color', 'paises_id', 'estados_id', 'municipios_id', 'codigo_postal'];

    protected $casts = [
        'codigo_postal' => 'array',
    ];


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
/*
    public function colonias(): BelongsTo
    {
        return $this->belongsTo(Colonias::class);
    }
   
    public function zoneLocations(): HasMany
    {
        return $this->hasMany(ZoneLocation::class);
    }
*/
    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

    public function zoneUser(): HasMany
    {
        return $this->hasMany(ZoneUser::class);
    }

}
