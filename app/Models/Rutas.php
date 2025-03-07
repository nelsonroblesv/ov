<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rutas extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'created_at',
        'regiones_id',
        'zonas_id',
        'tipo_semana',
        'tipo_cliente',
        'full_address',
        'visited',
        'sort'
    ];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function regiones(): BelongsTo
    {
        return $this->belongsTo(Regiones::class);
    }

    public function zonas(): BelongsTo
    {
        return $this->belongsTo(Zonas::class);
    }

}
