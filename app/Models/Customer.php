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
        'name',
        'email',
        'phone',
        'avatar',
        'birthday',
        'full_address',
        'latitude',
        'longitude',
        'front_image',
        'inside_image',
        'regiones_id',
        'zonas_id',
        'services',
        'reventa',
        'extra',
        'is_visible',
        'is_active',
        'is_preferred',
        'name_facturacion',
        'razon_social',
        'address_facturacion',
        'postal_code_facturacion',
        'tipo_cfdi',
        'tipo_razon_social',
        'cfdi_document',
        'user_id',
        'tipo_cliente',
        'simbolo',
        'created_at',
        'updated_at',
        'paquete_inicio_id',
        'alta_user_id'
    ];

    protected $appends = [
        'location',
    ];

    protected $casts = [
        'services' => 'array',
        'front_image' => 'array',
        'inside_image' => 'array',
    ];

    /**
     * ADD THE FOLLOWING METHODS TO YOUR Customer MODEL
     *
     * The 'latitude' and 'longitude' attributes should exist as fields in your table schema,
     * holding standard decimal latitude and longitude coordinates.
     *
     * The 'location' attribute should NOT exist in your table schema, rather it is a computed attribute,
     * which you will use as the field name for your Filament Google Maps form fields and table columns.
     *
     * You may of course strip all comments, if you don't feel verbose.
     */

    /**
     * Returns the 'latitude' and 'longitude' attributes as the computed 'location' attribute,
     * as a standard Google Maps style Point array with 'lat' and 'lng' attributes.
     *
     * Used by the Filament Google Maps package.
     *
     * Requires the 'location' attribute be included in this model's $fillable array.
     *
     * @return array
     */

    public function getLocationAttribute(): array
    {
        return [
            "lat" => (float)$this->latitude,
            "lng" => (float)$this->longitude,
        ];
    }

    /**
     * Takes a Google style Point array of 'lat' and 'lng' values and assigns them to the
     * 'latitude' and 'longitude' attributes on this model.
     *
     * Used by the Filament Google Maps package.
     *
     * Requires the 'location' attribute be included in this model's $fillable array.
     *
     * @param ?array $location
     * @return void
     */
    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location)) {
            $this->attributes['latitude'] = $location['lat'];
            $this->attributes['longitude'] = $location['lng'];
            unset($this->attributes['location']);
        }
    }

    /**
     * Get the lat and lng attribute/field names used on this table
     *
     * Used by the Filament Google Maps package.
     *
     * @return string[]
     */
    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'latitude',
            'lng' => 'longitude',
        ];
    }

    /**
     * Get the name of the computed location attribute
     *
     * Used by the Filament Google Maps package.
     *
     * @return string
     */
    public static function getComputedLocation(): string
    {
        return 'location';
    }


    /************************** Relationships *************************/

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function altaUser()
    {
        return $this->belongsTo(User::class, 'alta_user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Services::class);
    }

    public function regiones(): BelongsTo
    {
        return $this->belongsTo(Regiones::class, 'regiones_id');
    }

    public function bitacora()
    {
        return $this->hasMany(BitacoraProspeccion::class);
    }

    public function names()
    {
        return $this->hasMany(BitacoraCustomers::class);
    }

    public function rutas(): BelongsTo
    {
        return $this->belongsTo(Rutas::class, 'zonas_id');
    }

    public function paquete_inicio(): BelongsTo
    {
        return $this->belongsTo(PaquetesInicio::class, 'paquete_inicio_id');
    }
    
    public function zona()
    {
        return $this->belongsTo(Zonas::class, 'zonas_id');
    }
    
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}
