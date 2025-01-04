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
        'alias', 'name', 'email', 'phone', 'avatar', 'birthday',
        'paises_id', 'estados_id', 'municipios_id', 'colonias_id', 'full_address', 'latitude', 'longitude',
        'front_image', 'inside_image', 'extra', 'is_visible', 'is_active', 'is_preferred', 'name_facturacion', 'razon_social', 'address_facturacion',
        'postal_code_facturacion', 'tipo_cfdi', 'tipo_razon_social', 'cfdi_document', 'user_id'
    ];

   public function user(): BelongsTo
   {
        return $this->belongsTo(User::class);
    }

    public function paises(): BelongsTo
    {
        return $this->belongsTo(Paises::class);
    }

    public function estados(): BelongsTo
    {
        return $this->belongsTo(Estados::class);
    }

    public function municipios(): BelongsTo
    {
        return $this->belongsTo(Municipios::class);
    }

    public function colonias(): BelongsTo
    {
        return $this->belongsTo(Colonias::class);
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
