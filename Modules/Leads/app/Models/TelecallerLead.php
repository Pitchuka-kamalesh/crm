<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class TelecallerLead extends Model
{
    protected $table = 'crm.telecaller_leads';
    protected $primaryKey = 'applino';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'applino',
        'phoneno',
        'crmid',
        'empcode',
        'leadcount',
        'assigndatetime',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected $casts = [
        'leadcount' => 'integer',
        'assigndatetime' => 'datetime',
        'log_timestamp' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Crm\Models\CrmStudentRegistration::class, 'applino', 'applinano');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Employee::class, 'empcode', 'emp_code');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(\Modules\Leads\Models\TelecallerLeadTask::class, 'applino', 'applino');
    }

    public function scopeByEmployee(Builder $query, string $empCode): Builder
    {
        return $query->where('empcode', trim($empCode));
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }
        $clean = trim($term);
        return $query->where(function ($q) use ($clean) {
            $q->where('applino', 'like', "%{$clean}%")
              ->orWhere('phoneno', 'like', "%{$clean}%")
              ->orWhere('crmid', 'like', "%{$clean}%")
              ->orWhere('empcode', 'like', "%{$clean}%");
        });
    }
}

