<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'alias', 'name', 'email', 'phone', 'avatar', 'address', 'state', 'municipality', 'locality', 'zip_code',
        'contact', 'front_image', 'inside_image', 'coordinate', 'type', 'extra', 'is_visible', 'is_active',
        'user_id'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }
}
