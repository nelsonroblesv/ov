<?php

namespace App\Models;


use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'number', 'status', 'notes', 'grand_total', 'created_at', 'notas_venta', 
        'fecha_liquidacion', 'tipo_nota', 'tipo_semana_nota', 'dia_nota', 'created_at', 'updated_at', 
        'solicitado_por', 'registrado_por'
    ];

    protected $casts = [
        'notas_venta' => 'array',
        'fecha_liquidacion' => 'date',
        'grand_total' => 'decimal:2',
    ];

    public function customer() :BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function registrador() :BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function solicitador() :BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function items() :HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payments::class);
    }
}
