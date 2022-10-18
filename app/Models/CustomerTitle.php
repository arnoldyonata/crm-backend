<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerTitle extends Model
{
    protected $table = 'customer_title';

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
