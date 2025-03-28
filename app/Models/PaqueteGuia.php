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
        'zonas_id',
        'regiones_id',
        'estado',
        'created_at',
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
