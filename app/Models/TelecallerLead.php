<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelecallerLead extends Model
{
    protected $table = 'crm.telecaller_leads';
    
    // Composite PK handled via query builder or custom key definition
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
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'empcode', 'emp_code');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(TelecallerLeadTask::class, 'applino', 'applino');
    }
}

