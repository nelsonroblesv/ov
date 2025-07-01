<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'customer_type', 'zonas_id', 'regiones_id', 'factura', 'num_pedido', 'fecha_pedido', 
        'tipo_nota', 'tipo_semana_nota', 'periodo', 'semana', 'dia_nota', 'num_ruta', 
        'monto', 'estado_pedido', 'fecha_entrega', 'fecha_liquidacion',
        'distribuidor', 'entrega', 'reparto', 'observaciones',
        'notas_venta', 'registrado_por'
    ];

    protected $casts = [
        'notas_venta' => 'array',
        'fecha_liquidacion' => 'date',
        'fecha_pedido' => 'date',
        'fecha_entrega' => 'date',
        'monto' => 'decimal:2',
    ];

    public function customer() :BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function registrador() :BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payments::class);
    }

}
