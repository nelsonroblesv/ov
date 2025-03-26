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
        'created_at'
    ];

    protected $casts = [
        'testigo_1' => 'array',
        /*'testigo_2' => 'array'*/
    ];
    public function customers()
    {
        return $this->belongsTo(Customer::class);
    }
}
