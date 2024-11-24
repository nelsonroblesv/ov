<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    //
    protected $fillable = [
        'name','slug','url','description','thumbnail','category_id', 'visibility','availability', 
        'price', 'sku','shipping'
    ];

    public function category() :BelongsTo
    {
        return $this->belongsTo(Category::class);
    }  
    
    public function orderItems()
    {
        return $this->hasMany(Product::class);
    }
}
