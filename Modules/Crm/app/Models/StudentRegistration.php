<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentRegistration extends Model
{
    use HasFactory;

    protected $table = 'admissions.student_registration';
    protected $primaryKey = 'application_no';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'application_no',
        'register_no',
        'admission_year',
        'degree_level',
        'program_id',
        'program_name',
        'branch_id',
        'branch_name',
        'candidate_name',
        'candidate_mobile',
        'candidate_email',
        'dob',
        'gender',
        'aadhar_no',
        'state_id',
        'state_name',
        'district_id',
        'district_name',
        'current_step',
        'application_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function education(): HasMany
    {
        return $this->hasMany(\Modules\Dashboards\Models\StudentEducationInfo::class, 'application_no', 'application_no');
    }
}

