<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CredentialOwner extends Model
{
    protected $table = 'CredentialOwner';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'partitionid',
        'nid',
        'description',
        'startdate',
        'enddate',
        'credentialownergroupid',
        'isdeleted',
        'isactive',
        'property',
    ];

    protected $casts = [
        'startdate' => 'datetime',
        'enddate' => 'datetime',
        'isdeleted' => 'boolean',
        'isactive' => 'boolean',
        'property' => 'array',
    ];

    public function group()
    {
        return $this->belongsTo(
            CredentialOwnerGroup::class,
            'credentialownergroupid',
            'id'      
        );
    }
}
