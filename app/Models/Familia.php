<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Familia extends Model
{
    protected $fillable = [
        'name','slug','url','thumbnail','primary_color','is_active', 'description', 'categories', 'marcas_id'
    ];

    protected $casts = [
        'categories' => 'array',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function marcas(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
