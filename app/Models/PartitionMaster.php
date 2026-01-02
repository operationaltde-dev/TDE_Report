<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartitionMaster extends Model
{
    protected $table = 'PartitionMaster';

    protected $fillable = [
        'description',
        'isdeleted',
    ];

    protected $casts = [
        'isdeleted' => 'boolean',
    ];

    public $timestamps = false;
}
