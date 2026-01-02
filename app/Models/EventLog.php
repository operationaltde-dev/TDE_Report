<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    protected $table = 'EventLog';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'partitionid',
        'ts',
        'ts_received',
        'msgtype',
        'taskcode',
        'eventcode',
        'controllerid',
        'subcontrollerid',
        'hardwareid',
        'credentialid',
        'ownerid',
        'msgdescription',
        'controllerdescription',
        'subcontrollerdescription',
        'hardwaredescription',
        'credentialdescription',
        'ownerdescription',
    ];

    protected $casts = [
        'ts' => 'datetime',
        'ts_received' => 'datetime',
    ];

    public function credential()
    {
        return $this->belongsTo(
            Credential::class,
            'credentialid',
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