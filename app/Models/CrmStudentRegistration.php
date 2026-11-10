<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmStudentRegistration extends Model
{
    protected $table = 'crm.student_registration';
    protected $primaryKey = 'applinano';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'applinano',
        'crmid',
        'name',
        'phoneno',
        'campuscode',
        'emailid',
        'countryid',
        'stateid',
        'programlevel',
        'programid',
        'branchid',
        'ableto',
        'stage1',
        'stage2',
        'stage3',
        'stage4',
        'stage5',
        'stage6',
        'stage7',
        'stage8',
        'std_status',
        'remarks',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campuscode', 'campus_code');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'programid', 'program_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branchid', 'branch_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'countryid', 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'stateid', 'state_id');
    }

    public function personalInfo()
    {
        return $this->hasOne(StudentPersonalInfo::class, 'applino', 'applinano');
    }

    public function academicDetails()
    {
        return $this->hasOne(StudentAcademicDetail::class, 'applino', 'applinano');
    }

    public function telecallerLead()
    {
        return $this->hasOne(TelecallerLead::class, 'applino', 'applinano');
    }

    public function telecallerTasks()
    {
        return $this->hasMany(TelecallerLeadTask::class, 'applino', 'applinano');
    }
}

