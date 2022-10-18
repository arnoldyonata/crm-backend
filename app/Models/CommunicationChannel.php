<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CommunicationChannel extends Model
{
    public const SMS = 1;

    public const EMAIL = 2;

    public const WHATSAPP = 3;

    protected $table = 'communication_channel';

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class)
            ->as('contactPreference');
    }
}
