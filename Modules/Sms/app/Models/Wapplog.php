<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Model;

class Wapplog extends Model
{
    protected $table = 'crm.wapplogs';
    public $timestamps = false;

    protected $fillable = [
        'phoneno',
        'template_name',
        'status',
        'response',
        'log_timestamp',
    ];

    protected $casts = [
        'log_timestamp' => 'datetime',
    ];
}

