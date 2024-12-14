<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'color'
    ];

    public function ubicaciones()
    {
        return $this->hasMany(ZoneLocation::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
