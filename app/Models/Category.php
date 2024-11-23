<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name','slug','url','thumbnail','primary_color','is_active', 'description'
    ];
}
