<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    protected $table = 'customer';

    public function icType(): BelongsTo
    {
        return $this->belongsTo(IcType::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(CustomerTitle::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function icColor(): BelongsTo
    {
        return $this->belongsTo(IcColor::class);
    }

    public function communicationChannels(): BelongsToMany
    {
        return $this->belongsToMany(CommunicationChannel::class)
            ->as('contactPreference');
    }
}
