<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IcColor extends Model
{
    public const YELLOW = 1;

    public const GREEN = 2;

    public const RED = 3;

    protected $table = 'ic_color';

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
