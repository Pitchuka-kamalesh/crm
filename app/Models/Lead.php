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
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected function casts(): array
    {
        return [
            'enquirydate' => 'date',
            'log_timestamp' => 'datetime',
        ];
    }
}
