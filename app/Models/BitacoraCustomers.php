<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraCustomers extends Model
{
    protected $fillable = [
        'user_id',
        'customers_id',
        'show_video',
        'notas',
        'testigo_1',
        'testigo_2',
        'created_at',
        'status',
        'tipo_visita',
        'foto_entrega',
        'foto_stock_antes',
        'foto_stock_despues',
        'foto_lugar_cerrado',
        'foto_stock_regular',
        'foto_evidencia_prospectacion',
    ];

    protected $casts = [
        'foto_evidencia_prospectacion' => 'array',
    ];
    public function customers()
    {
        return $this->belongsTo(Customer::class);
    }
}
