<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'crm.leads';
    protected $primaryKey = 'phoneno';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'applino',
        'phoneno',
        'phnostatus',
        'enquirydate',
        'empcode',
        'allocated_at',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected function casts(): array
    {
        return [
            'enquirydate' => 'date',
            'allocated_at' => 'datetime',
            'log_timestamp' => 'datetime',
        ];
    }
}
