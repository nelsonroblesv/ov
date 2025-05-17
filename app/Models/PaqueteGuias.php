<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaqueteGuias extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'periodo',
        'semana',
        'num_semana',
        'regiones_id',
        'estado',
        'created_at',
        'updated_at',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function regiones(): BelongsTo
    {
        return $this->belongsTo(Regiones::class, 'regiones_id');
    }

    public function guias()
    {
        return $this->hasMany(Guia::class);
    }
}
