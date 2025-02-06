<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Services extends Model
{
    protected $fillable = ['name'];

    public function prospectos(): BelongsTo
    {
        return $this->belongsTo(Prospectos::class);
    }

}
