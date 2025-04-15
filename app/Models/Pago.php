<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = ['cobranza_id', 'monto', 'comprobante', 'tipo_pago', 
            'created_at',  'updated_at', 'periodo', 'semana', 'tipo_semana'];

    public function cobranza(): BelongsTo
    {
        return $this->belongsTo(Cobranza::class);
    }
}
