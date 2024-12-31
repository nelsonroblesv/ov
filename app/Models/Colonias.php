<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colonias extends Model
{
    protected $fillable = ['nombre', 'ciudad', 'municipios_id', 'asentamiento', 'codigo_postal'];

    public function municipios(): BelongsTo
    {
        return $this->belongsTo(Municipios::class);
    }

}
