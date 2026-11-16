<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class LoginOtp extends Model
{
    protected $table = 'master.login_otp';
    protected $primaryKey = 'otp_id';
    public $timestamps = false;

    protected $fillable = [
        'usercode',
        'identifier',
        'otp_code',
        'purpose',
        'is_used',
        'expires_at',
        'attempt_count',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'expires_at' => 'datetime',
            'log_timestamp' => 'datetime',
        ];
    }
}
