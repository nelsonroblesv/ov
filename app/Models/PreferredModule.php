<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreferredModule extends Model
{
    protected $fillable = ['module_name', 'grand_total'];

    public function preferredItems()
    {
        return $this->hasMany(PreferredModuleItem::class);
    }

}
