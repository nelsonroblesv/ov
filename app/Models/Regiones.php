<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regiones extends Model
{
    protected $fillable = ['name', 'description'];

    public function zonas(): HasMany
    {
        return $this->hasMany(Zonas::class, 'regiones_id');
    }
}
