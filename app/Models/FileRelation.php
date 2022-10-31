<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileRelation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'file_relation';
}
