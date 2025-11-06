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
        'customer_id',
        'customer_type',
        'zonas_id',
        'regiones_id',
        'factura',
        'num_pedido',
        'id_nota',
        'tipo_nota',
        'tipo_semana_nota',
        'periodo',
        'semana',
        'dia_nota',
        'num_ruta',
        'monto',
        'estado_pedido',
        'fecha_entrega',
        'fecha_liquidacion',
        'distribuidor',
        'reparto',
        'observaciones',
        'notas_venta',
        'registrado_por',
        'day',
        'month',
        'year',
        'real_id',
        'estado_general',
        'signature', 
        'is_signed'
    ];

    protected $casts = [
        'notas_venta' => 'array',
        'fecha_liquidacion' => 'date',
        'fecha_entrega' => 'date',
        'monto' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zonas::class, 'zonas_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Regiones::class, 'regiones_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payments::class);
    }

    public function userDistribuidor()
    {
        return $this->belongsTo(User::class, 'distribuidor');
    }

    public function userReparto()
    {
        return $this->belongsTo(User::class, 'reparto');
    }

    public function visitas()
    {
        return $this->hasMany(Visita::class);
    }

    public function cobros()
    {
        return $this->hasMany(Cobro::class);
    }

    public function items() :HasMany
    {
        return $this->hasMany(PedidosItems::class);
    }
}
