<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReaderGroup extends Model
{
    protected $table = 'ReaderGroup';

    protected $fillable = [
        'readergroupmasterid',
        'deviceid',
    ];

    public $timestamps = false;

    public function master()
    {
        return $this->belongsTo(
            ReaderGroupMaster::class,
            'readergroupmasterid',
            'id'      
        );
    }

    public function hardware()
    {
        return $this->belongsTo(
            Hardware::class,
            'deviceid',
            'id'      
        );
    }

    public function credential_access()
    {
        return $this->belongsTo(
            CredentialAccess::class,
            'readergroupmasterid',
            'accessgroupmasterid'      
        );
    }
}