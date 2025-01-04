<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prospectos extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'notes',
        'paises_id', 'estados_id', 'municipios_id', 'colonias_id', 
        'full_address', 'latitude', 'longitude',
        'user_id'
    ];

    public function user(): BelongsTo
    {
         return $this->belongsTo(User::class);
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
