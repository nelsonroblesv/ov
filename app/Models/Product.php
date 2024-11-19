<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name','slug','url','description','image','category_id', 'visibility','availability', 
        'price', 'sku','shipping'
    ];
}
