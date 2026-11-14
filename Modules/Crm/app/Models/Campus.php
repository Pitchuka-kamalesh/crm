<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $table = 'master.campuses';
    protected $primaryKey = 'campus_id';
    public $timestamps = false;

    protected $fillable = [
        'campus_id',
        'campus_code',
        'campus_short_name',
        'campus_long_name',
        'address_line1',
        'address_line2',
        'city',
        'district',
        'state',
        'country',
        'pincode',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'established_date',
        'start_date',
        'approve_board',
        'approve_board_code',
        'approve_board_date',
        'accreditation',
        'accreditation_valid_till',
        'is_active',
        'lock_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];
}

