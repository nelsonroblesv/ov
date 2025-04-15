<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cobranza extends Model
{
    protected $fillable = ['customer_id', 'codigo', 'saldo_total', 'created_by', 'created_at', 
                    'updated_at', 'periodo', 'semana', 'tipo_semana'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function getSaldoPendienteAttribute(): float
    {
        return $this->saldo_total - $this->pagos()->sum('monto');
    }

    public function getEstadoAttribute()
    {
        $pendiente = $this->saldo_total - $this->pagos()->sum('monto');

        if ($pendiente <= 0) {
            return 'Pagado';
        }

        // Se considera vencido si pasaron más de 15 días desde que se creó
        if (now()->greaterThan($this->created_at->addDays(15))) {
            return 'Vencido';
        }

        return 'Pendiente';
    }
}
