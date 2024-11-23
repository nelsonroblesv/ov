<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'alias', 'name', 'email', 'phone', 'avatar', 'address', 'state_id', 'municipality_id', 'locality', 'zip_code',
        'contact', 'front_image', 'inside_image', 'coordinate', 'type', 'extra', 'is_visible', 'is_active'
    ];

   /* public function users(){
        return $this->belongsTo(User::class);
    }*/

    public function state(){
        return $this->belongsTo(State::class);
    }
    public function municipality(){
        return $this->belongsTo(Municipality::class);
    }
}
