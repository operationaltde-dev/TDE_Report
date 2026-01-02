<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CredentialAccess extends Model
{
    protected $table = 'CredentialAccess';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'credentialid',
        'accessgroupmasterid',
    ];

    public function credential()
    {
        return $this->belongsTo(
            Credential::class,
            'credentialid',
            'id'      
        );
    }
}
