<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraCustomers extends Model
{
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customers_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function visita()
    {
        return $this->belongsTo(Visita::class);
    }

    public function cobros()
    {
        return $this->hasMany(Cobro::class);
    }
}
