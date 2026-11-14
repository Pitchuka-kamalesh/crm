<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'crm.countries';
    protected $primaryKey = 'country_id';
    public $timestamps = false;

    protected $fillable = [
        'country_id',
        'country_code',
        'country_name',
        'country_phonecode',
        'lock_status',
        'log_userid',
        'log_timestamp',
        'log_ipaddress',
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id', 'country_id');
    }
}
