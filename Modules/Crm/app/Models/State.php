<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $table = 'crm.states';
    protected $primaryKey = 'state_id';
    public $timestamps = false;

    protected $fillable = [
        'state_id',
        'state_code',
        'state_name',
        'country_id',
        'lock_status',
        'log_userid',
        'log_timestamp',
        'log_ipaddress',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'STATE_ID', 'state_id');
    }
}
