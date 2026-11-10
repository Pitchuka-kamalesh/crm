<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleAssigned extends Model
{
    protected $table = 'master.roles_assigned';
    public $timestamps = false;
    protected $primaryKey = 'emp_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'emp_id',
        'campus_id',
        'roles_role_id',
        'start_date',
        'end_date',
        'last_access_date',
        'log_userid',
        'log_timestamp',
        'log_ipaddress',
    ];

    protected $casts = [
        'roles_role_id' => 'integer',
        'campus_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_access_date' => 'datetime',
        'log_timestamp' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_code');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'roles_role_id', 'role_id');
    }
}

