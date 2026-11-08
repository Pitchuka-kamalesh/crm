<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    protected $table = 'crm.district';
    protected $primaryKey = 'DISTRICT_ID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'DISTRICT_ID',
        'DISTRICT_CODE',
        'DISTRICT_NAME',
        'STATE_ID',
        'COUNTRY_ID',
        'LOCK_STATUS',
        'LOG_USERID',
        'LOG_TIMESTAMP',
        'LOG_IPADDRESS',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'STATE_ID', 'state_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'COUNTRY_ID', 'country_id');
    }
}
