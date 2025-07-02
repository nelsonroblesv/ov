<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regiones extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function zonas(): HasMany
    {
        return $this->hasMany(Zonas::class, 'regiones_id');
    }

    public function rutas(): HasMany
    {
        return $this->hasMany(Rutas::class);
    }

     public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

}
