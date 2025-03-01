<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'number', 'status', 'notes', 'grand_total', 'created_at', 'notas_venta'
    ];

    protected $casts = [
        'notas_venta' => 'array'
    ];

    public function customer() :BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items() :HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
