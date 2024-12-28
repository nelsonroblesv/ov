<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'alias', 'name', 'email', 'phone', 'avatar', 'address', 'state_id', 'birthday',
        'municipality_id', 'locality', 'zip_code', 'front_image', 'inside_image', 'coordinate', 
        'type', 'extra', 'is_visible', 'is_active', 'is_preferred', 'name_facturacion', 'razon_social', 'address_facturacion',
        'postal_code_facturacion', 'tipo_cfdi', 'tipo_razon_social', 'cfdi_document', 'user_id', 'zone_id'
    ];

   public function user(): BelongsTo
   {
        return $this->belongsTo(User::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function municipality(): BelongsTo
    {
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

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
