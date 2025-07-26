<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $fillable = [
        'pedido_id',
        'user_id',
        'fecha_visita',
        'tipo_visita',
        'notas',
        'evidencias',
    ];

    protected $casts = [
        'evidencias' => 'array',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cobro()
    {
        return $this->hasOne(Cobro::class);
    }
}
