<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $table = 'Credential';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'partitionid',
        'ownerid',
        'description',
        'startdate',
        'enddate',
        'neverexpire',
        'isdeleted',
        'isactive',
        'primaryelevatorgroup',
        'hasadditionalelevatorgroups',
        'rawcardnumber',
        'cardnumber',
        'pinnumber',
        'lastcredentialread',
        'lastcredentialreader',
        'lastcredentialreadergrant',
        'hostgrant',
        'property',
    ];

    protected $casts = [
        'startdate' => 'datetime',
        'enddate' => 'datetime',
        'neverexpire' => 'boolean',
        'isdeleted' => 'boolean',
        'isactive' => 'boolean',
        'hasadditionalelevatorgroups' => 'boolean',
        'lastcredentialread' => 'datetime',
        'lastcredentialreadergrant' => 'boolean',
        'hostgrant' => 'boolean',
        'property' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(
            CredentialOwner::class,
            'ownerid',
            'id'      
        );
    }

    public function partition()
    {
        return $this->belongsTo(
            PartitionMaster::class,
            'partitionid',
            'id'      
        );
    }
}