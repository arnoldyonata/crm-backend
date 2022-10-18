<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    public const BRUNEI = 1;

    public const MALAYSIA = 2;

    protected $table = 'country';

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
