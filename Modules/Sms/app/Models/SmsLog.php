<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $table = 'master.smslogs';
    protected $primaryKey = 'smsId';
    public $timestamps = false;

    protected $fillable = [
        'smsuser',
        'smsregisterno',
        'smstophone',
        'jiotraceid',
        'smstext',
        'datetime',
        'ipaddr',
        'status',
        'var1',
        'var2',
        'var3',
        'var4',
        'var5',
    ];

    protected $casts = [
        'datetime' => 'datetime',
    ];
}
