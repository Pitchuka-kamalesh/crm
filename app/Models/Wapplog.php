<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wapplog extends Model
{
    protected $table = 'master.wapp_logs';
    protected $primaryKey = 'id';
    public $timestamps = true;

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

    protected function casts(): array
    {
        return [
            'response_payload' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

