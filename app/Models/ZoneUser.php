<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoneUser extends Model
{
    protected $fillable = ['user_id', 'zone_id'];

   public function zone(): BelongsTo
   {
       return $this->belongsTo(Zone::class);
   }
 
   public function user(): BelongsTo
   {
       return $this->belongsTo(User::class);
   }
}
