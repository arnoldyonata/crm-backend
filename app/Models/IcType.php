<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IcType extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'ic_type';
}
