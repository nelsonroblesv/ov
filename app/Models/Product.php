<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    //
    protected $fillable = [
        'name','slug','url','description','thumbnail','marca_id', 'familia_id', 'category_id', 
        'visibility','availability', 'shipping',
        'price_distribuidor', 'price_salon', 'price_publico', /*'sku'*/
    ];

    public function marca() :BelongsTo
    {
        return $this->belongsTo(Marca::class);
    } 

    public function familia() :BelongsTo
    {
        return $this->belongsTo(Familia::class);
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
