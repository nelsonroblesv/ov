<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaCobranzaDetalle extends Model
{
    protected $fillable = [
        'entrega_cobranza_id',
        'customer_id',
        'user_id',
        'tipo',
        'notas'
    ];

    public function entregaCobranza()
    {
        return $this->belongsTo(EntregaCobranza::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
}
