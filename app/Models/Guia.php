<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'paquete_guias_id',
        'numero_guia',
        'estado',
    ];

    public function paquete()
    {
        return $this->belongsTo(PaqueteGuias::class, 'paquete_guias_id');
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
}
