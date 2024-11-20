<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name','slug','url','description','thumbnail','category_id', 'visibility','availability', 
        'price', 'sku','shipping'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
