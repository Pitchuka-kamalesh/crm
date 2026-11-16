<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'master.programs';
    protected $primaryKey = 'program_id';
    public $timestamps = false;

    protected $fillable = [
        'program_id',
        'program_type_id',
        'program_short_name',
        'program_long_name',
        'is_active',
        'lock_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];
}

