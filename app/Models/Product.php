<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    //
    protected $fillable = [
        'name','slug','url','description','thumbnail','category_id', 'visibility','availability', 
        'price_distribuidor', 'price_salon', 'price_publico', 'sku','shipping'
    ];

    public function category() :BelongsTo
    {
        return $this->belongsTo(Category::class);
    }  
    
    public function orderItems(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function preferredModuleItems()
    {
        return $this->hasMany(PreferredModuleItem::class);
    }

}
