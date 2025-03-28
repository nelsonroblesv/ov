<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'paquete_guia_id',
        'numero_guia',
        'estado',
    ];

    public function paquete()
    {
        return $this->belongsTo(PaqueteGuia::class, 'paquete_guia_id');
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
}
