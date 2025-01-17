<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraProspeccion extends Model
{
    //
    protected $fillable = ['prospectos_id', 'notas'];


    public function prospectos(){
        return $this->belongsTo(Prospectos::class);
    }
}
