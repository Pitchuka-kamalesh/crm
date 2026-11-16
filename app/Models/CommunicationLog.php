<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationLog extends Model
{
    use HasFactory;

    protected $table = 'master.wapp_logs';

    protected $fillable = [
        'channel',
        'wauser',
        'waregisterno',
        'wapptophone',
        'jiotraceid',
        'template_name',
        'wapptext',
        'var1',
        'var2',
        'status',
        'ipaddr',
        'response_payload',
    ];

    protected $casts = [
        'response_payload' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
