<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'master.employees';
    protected $primaryKey = 'emp_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'emp_code',
        'full_name',
        'ofc_mailid',
        'personal_mailid',
        'phone_primary',
        'phone_secondary',
        'designation_id',
        'department_id',
        'campus_id',
        'role_id',
        'emp_type',
        'date_of_join',
        'is_active',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];
}

