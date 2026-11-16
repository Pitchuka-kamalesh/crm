<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSchedule extends Model
{
    protected $table = 'finance.exam_schedule';
    protected $primaryKey = 'applino';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'applino',
        'crmid',
        'phoneno',
        'examdate',
        'exam_slot',
        'exam_status',
        'loguserid',
        'logdatetime',
        'ipaddress',
    ];

    protected $casts = [
        'examdate' => 'date',
        'exam_status' => 'integer',
        'logdatetime' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Crm\Models\CrmStudentRegistration::class, 'applino', 'applinano');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('examdate', $date);
    }

    public function scopeForSlot($query, string $slot)
    {
        return $query->where('exam_slot', $slot);
    }
}

