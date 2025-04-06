<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GestionRutas extends Model
{
    protected $fillable = [
        'user_id',
        'dia_semana',
        'tipo_semana',
        'customer_id',
        'region_id',
        'zona_id',
        'orden',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
