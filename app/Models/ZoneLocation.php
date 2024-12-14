<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneLocation extends Model
{
    protected $fillable = ['zone_id', 'state_id', 'municipality_id'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

}
