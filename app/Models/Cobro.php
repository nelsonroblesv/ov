<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cobro extends Model
{
    protected $fillable = [
        'pedido_id',
        'visita_id',
        'user_id',
        'monto',
        'fecha_pago',
        'tipo_pago',
        'comentarios',
        'comprobantes',
        'aprobado',
    ];

    protected $casts = [
        'comprobantes' => 'array',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visita()
    {
        return $this->belongsTo(Visita::class);
    }
}
