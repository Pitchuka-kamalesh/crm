<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Lead extends Model
{
    protected $table = 'crm.leads';
    protected $primaryKey = 'phoneno';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'applino',
        'phoneno',
        'phnostatus',
        'enquirydate',
        'empcode',
        'allocated_at',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected function casts(): array
    {
        return [
            'enquirydate' => 'date',
            'allocated_at' => 'datetime',
            'log_timestamp' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Crm\Models\CrmStudentRegistration::class, 'applino', 'applinano');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Employee::class, 'empcode', 'emp_code');
    }

    public function telecallerLead(): HasOne
    {
        return $this->hasOne(\Modules\Leads\Models\TelecallerLead::class, 'applino', 'applino');
    }

    public function scopeAllocated(Builder $query): Builder
    {
        return $query->whereNotNull('empcode')->where('empcode', '!=', '');
    }

    public function scopeUnallocated(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('empcode')->orWhere('empcode', '');
        });
    }

    public function scopeForEmployee(Builder $query, string $empCode): Builder
    {
        return $query->where('empcode', trim($empCode));
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('enquirydate', $date);
    }
}

