<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaqueteGuias extends Model
{
    protected $fillable = [
        'periodo',
        'semana',
        'num_semana',
        'regiones_id',
        'estado',
        'user_id'
    ];

    public function region()
    {
        return $this->belongsTo(Regiones::class, 'regiones_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
