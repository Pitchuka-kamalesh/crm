<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wapplog extends Model
{
    protected $table = 'crm.wapplogs';
    protected $primaryKey = 'waId';
    public $timestamps = false;

    protected $fillable = [
        'wauser',
        'waregisterno',
        'wapptophone',
        'jiotraceid',
        'wapptext',
        'datetime',
        'ipaddr',
        'status',
        'var1',
        'var2',
        'var3',
        'var4',
        'var5',
    ];

    protected function casts(): array
    {
        return [
            'datetime' => 'datetime',
        ];
    }
}

