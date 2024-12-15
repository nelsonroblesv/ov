<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneLocation extends Model
{
    protected $fillable = ['zone_id', 'municipality_id'];

   // Relación con Zone: Una ubicación pertenece a una zona
   public function zone()
   {
       return $this->belongsTo(Zone::class);
   }
   // Relación con Municipality: Una ubicación pertenece a un municipio
   public function municipality()
   {
       return $this->belongsTo(Municipality::class);
   }

}
