<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IcType extends Model
{
    protected $table = 'ic_type';

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
