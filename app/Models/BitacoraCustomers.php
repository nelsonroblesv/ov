<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraCustomers extends Model
{
    protected $fillable = ['customers_id','show_video', 'notas', 
                            'testigo_1', 'testigo_2', 'created_at'];


    public function customers(){
        return $this->belongsTo(Customer::class);
    }
}
