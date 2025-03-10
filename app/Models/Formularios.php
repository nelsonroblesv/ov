<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formularios extends Model
{
    protected $fillable = [
       'eventos_id', 'fecha_registro', 'nombre', 'ciudad', 'email', 'telefono'
    ];
}
