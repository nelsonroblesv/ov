<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaCobranza extends Model
{
    protected $fillable = [
        'alta_user_id',
        'periodo',
        'semana_mes',
        'semana_anio',
        'tipo_semana',
        'fecha_inicio',
        'fecha_fin'
    ];

    public function altaUser()
    {
        return $this->belongsTo(User::class, 'alta_user_id');
    }

    public function detalles()
{
    return $this->hasMany(EntregaCobranzaDetalle::class);
}

}
