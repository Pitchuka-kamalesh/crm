<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelecallerLeadTask extends Model
{
    protected $table = 'crm.telecaller_leads_tasks';
    protected $primaryKey = 'leadtaskid';
    public $timestamps = false;

    protected $fillable = [
        'applino',
        'phoneno',
        'crmid',
        'empcode',
        'stage_name',
        'stage_calltime',
        'stage_call_duration',
        'stage_remark',
        'stage_callperson',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected $casts = [
        'leadtaskid' => 'integer',
        'stage_calltime' => 'datetime',
        'log_timestamp' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(TelecallerLead::class, 'applino', 'applino');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'empcode', 'emp_code');
    }
}

