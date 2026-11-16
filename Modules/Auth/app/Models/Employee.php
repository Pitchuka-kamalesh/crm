<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $table = 'master.employees';
    protected $primaryKey = 'emp_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'emp_code',
        'full_name',
        'ofc_mailid',
        'personal_mailid',
        'phone_primary',
        'phone_secondary',
        'designation_id',
        'department_id',
        'campus_id',
        'role_id',
        'emp_type',
        'date_of_join',
        'is_active',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(\Modules\Leads\Models\Lead::class, 'empcode', 'emp_code');
    }

    public function telecallerLeads(): HasMany
    {
        return $this->hasMany(\Modules\Leads\Models\TelecallerLead::class, 'empcode', 'emp_code');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(\Modules\Leads\Models\TelecallerLeadTask::class, 'empcode', 'emp_code');
    }

    public function user(): HasOne
    {
        return $this->hasOne(\Modules\Auth\Models\User::class, 'usercode', 'emp_code');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\Role::class, 'role_id', 'role_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(\Modules\Crm\Models\Campus::class, 'campus_id', 'campus_id');
    }

    public function roleAssigned(): HasOne
    {
        return $this->hasOne(\Modules\Auth\Models\RoleAssigned::class, 'emp_id', 'emp_code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeTelecallers($query)
    {
        return $query->where(function ($q) {
            $q->where('role_id', 4)
              ->orWhereHas('roleAssigned', fn($rq) => $rq->where('roles_role_id', 4));
        });
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }
        $clean = trim($term);
        return $query->where(function ($q) use ($clean) {
            $q->where('emp_code', 'like', "%{$clean}%")
              ->orWhere('full_name', 'ilike', "%{$clean}%")
              ->orWhere('ofc_mailid', 'ilike', "%{$clean}%")
              ->orWhere('phone_primary', 'like', "%{$clean}%");
        });
    }
}

