<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cobro extends Model
{
    protected $fillable = [
        'pedidos_id',
        'visitas_id',
        'users_id',
        'monto',
        'fecha_pago',
        'tipo_pago',
        'comentarios',
        'comprobantes',
        'aprobado',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
