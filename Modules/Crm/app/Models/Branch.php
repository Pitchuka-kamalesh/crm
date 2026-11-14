<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'master.branches';
    protected $primaryKey = 'branch_id';
    public $timestamps = false;

    protected $fillable = [
        'branch_id',
        'program_type_id',
        'program_id',
        'branch_short_name',
        'branch_long_name',
        'is_active',
        'lock_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];
}

