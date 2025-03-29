<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaquetesInicio extends Model
{
    protected $fillable = [
        'prefijo',
        'nombre',
        'descripcion',
        'imagen',
        'precio',
    ];
}
