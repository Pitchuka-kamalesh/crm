<?php

namespace Modules\Dashboards\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEducationInfo extends Model
{
    use HasFactory;

    protected $table = 'admissions.student_education_info';
    protected $primaryKey = 'edu_id';
    public $timestamps = false;

    protected $fillable = [
        'application_no',
        'register_no',
        'qual_level',
        'qual_name',
        'institution_name',
        'institution_type',
        'board_university',
        'affiliated_to',
        'medium',
        'study_mode',
        'start_year',
        'end_year',
        'year_of_passing',
        'hall_ticket_no',
        'roll_no',
        'result_status',
        'marks_obtained',
        'max_marks',
        'percentage',
        'grade',
        'cgpa',
        'city',
        'state',
        'country',
        'remarks',
        'lock_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Crm\Models\StudentRegistration::class, 'application_no', 'application_no');
    }
}

