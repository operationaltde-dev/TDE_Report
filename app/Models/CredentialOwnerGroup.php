<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CredentialOwnerGroup extends Model
{
    protected $table = 'CredentialOwnerGroup';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'partitionid',
        'description',
        'isdeleted',
    ];

    protected $casts = [
        'isdeleted' => 'boolean',
    ];
}