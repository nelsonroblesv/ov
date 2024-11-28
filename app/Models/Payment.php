<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    //
    protected $fillable = [
        'order_id', 'customer_id', 'amount', 'type', 'voucher', 'notes'
    ];

    public function customer() :BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order() :BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
