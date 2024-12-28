<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreferredModule extends Model
{
    protected $fillable = [
        'module_name',
        'module_description',
        'module_cost',
    ];
}
