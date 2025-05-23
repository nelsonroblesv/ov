<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payments extends Model
{
    protected $fillable = [
        'customer_id', 'user_id', 'importe', 'tipo', 'voucher', 'notas', 'created_at', 'updated_at', 'is_verified'
    ];

    protected $casts = [
        'importe' => 'decimal:2',
        'is_verified' => 'boolean',
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
