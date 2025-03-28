<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaqueteGuia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'periodo',
        'semana',
        'zona_id',
        'region_id',
        'estado',
    ];

    public function zona()
    {
        return $this->belongsTo(Zonas::class);
    }

    public function region()
    {
        return $this->belongsTo(Regiones::class);
    }

    public function guias()
    {
        return $this->hasMany(Guia::class);
    }
}
