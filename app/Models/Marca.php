<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marca extends Model
{
    protected $fillable = ['name', 'description', 'logo', 'is_active'];

    public function familia(): HasMany
    {
        return $this->hasMany(Familia::class);
    }

}
