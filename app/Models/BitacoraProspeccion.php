<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraProspeccion extends Model
{
    //
    protected $fillable = ['prospectos_id', 'notas', 'testigo_1', 'testigo_2'];


    public function prospectos(){
        return $this->belongsTo(Prospectos::class);
    }
}
