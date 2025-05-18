<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guias extends Model
{
    protected $fillable = [
        'paquete_guias_id',
        'numero_guia',
        'recibido',
        'foto_caja',
    ];

    public function paqueteGuias()
    {
        return $this->belongsTo(PaqueteGuias::class);
    }
}
