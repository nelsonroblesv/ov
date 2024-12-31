<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paises extends Model
{
    protected $fillable = ['nombre'];

    public function estados(): HasMany
    {
        return $this->hasMany(Estados::class);
    }
}
