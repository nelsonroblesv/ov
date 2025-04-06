<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'asunto',
        'mensaje',
        'status',
        'created_at',
        'updated_at',
    ];
}
