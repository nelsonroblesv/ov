<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferredModuleItem extends Model
{
    protected $fillable = ['preferred_module_id', 'product_id', 'quantity', 
                            'total_price_publico', 'total_price_salon'];

    public function preferredModule()
    {
        return $this->belongsTo(PreferredModule::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
