<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    protected $table = 'master.branches';
    protected $primaryKey = 'branch_id';
    public $timestamps = false;

    protected $fillable = [
        'branch_id',
        'program_id',
        'campus_id',
        'branch_code',
        'branch_short_name',
        'branch_long_name',
        'branch_seprt_code',
        'branch_start_year',
        'lock_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}

