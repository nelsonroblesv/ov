<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidosItems extends Model
{
        protected $fillable = [
        'pedidos_id', 'product_id', 'quantity', 'price_publico', 'total_price'
    ];

    public function pedido() :BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function product() :BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
