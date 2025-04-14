<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbicacionUsuario extends Model
{
    protected $fillable = ['user_id', 'latitud', 'longitud', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
