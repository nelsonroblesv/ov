<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function municipality()
    {
        return $this->hasMany(Municipality::class);
    }





    public function zoneState()
    {
        return $this->hasMany(ZoneLocation::class);
    }
}