<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaCobranza extends Model
{
    protected $fillable = [
        'fecha_programada',
        'alta_user_id'
    ];

    public function altaUser()
    {
        return $this->belongsTo(User::class, 'alta_user_id');
    }

}
