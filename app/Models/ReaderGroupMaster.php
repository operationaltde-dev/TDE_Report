<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReaderGroupMaster extends Model
{
    protected $table = 'ReaderGroupMaster';

    protected $fillable = [
        'description',
        'isdeleted',
    ];

    protected $casts = [
        'isdeleted' => 'boolean',
    ];

    public $timestamps = false;
}
