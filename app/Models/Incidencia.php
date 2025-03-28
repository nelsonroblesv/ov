<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guia_id',
        'notas',
        'foto',
        'user_id',
    ];

    public function guia()
    {
        return $this->belongsTo(Guia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
