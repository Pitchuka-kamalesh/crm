<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'master.roles';
    protected $primaryKey = 'role_id';
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'role_name',
        'role_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected $casts = [
        'role_id' => 'integer',
        'role_status' => 'integer',
        'log_timestamp' => 'datetime',
    ];
}

