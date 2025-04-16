<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbicacionUsuario extends Model
{
    protected $fillable = [
        'user_id',
        'latitud',
        'longitud',
        'created_at',
        'location',
    ];

    protected $appends = [
        'location',
    ];

    public function getLocationAttribute(): array
    {
        return [
            "lat" => (float)$this->latitud,
            "lng" => (float)$this->longitud,
        ];
    }

    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location))
        {
            $this->attributes['latitud'] = $location['lat'];
            $this->attributes['longitud'] = $location['lng'];
            unset($this->attributes['location']);
        }
    }

    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'latitud',
            'lng' => 'longitud',
        ];
    }

    public static function getComputedLocation(): string
    {
        return 'location';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
