<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $fillable = [
        'pedidos_id',
        'users_id',
        'fecha_visita',
        'tipo_visita',
        'observaciones',
        'evidencias',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedidos_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
