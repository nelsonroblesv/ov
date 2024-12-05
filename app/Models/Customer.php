<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    //
    protected $fillable = [
        'alias', 'name', 'email', 'phone', 'avatar', 'address', 'state_id', 
        'municipality_id', 'locality', 'zip_code', 'front_image', 'inside_image', 'coordinate', 
        'type', 'extra', 'is_visible', 'is_active', 'name_facturacion', 'razon_social', 'address_facturacion',
        'postal_code_facturacion', 'tipo_cfdi', 'tipo_razon_social', 'cfdi_document'

        //'contact'
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
