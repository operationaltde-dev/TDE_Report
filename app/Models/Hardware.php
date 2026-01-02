<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hardware extends Model
{
    protected $table = 'Hardware';

    protected $fillable = [
        'partitionid',
        'description',
        'type',
        'parentid',
        'parenttype',
        'property',
        'isdeleted',
    ];

    public $timestamps = false;
}
