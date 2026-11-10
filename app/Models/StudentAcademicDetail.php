<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademicDetail extends Model
{
    use HasFactory;

    protected $table = 'crm.student_academic_details';
    protected $primaryKey = 'applino';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'crmid',
        'applino',
        'phoneno',
        'progmid',
        'branchid',

        // 10th grade
        'tenth_school_name',
        'tenth_study_board',
        'tenth_school_city',
        'tenth_school_district',
        'tenth_roll_no',
        'tenth_passed_year',
        'tenth_marks_type',
        'tenth_marks',
        'tenth_cgpa',

        // 12th / PUC
        'pu2_college_name',
        'pu2_study_board',
        'pu2_college_city',
        'pu2_college_district',
        'pu2_roll_no',
        'pu2_passed_year',
        'pu2_marks_type',
        'pu2_marks',
        'pu2_cgpa',
        'pu2_current_status',

        // UG
        'ug_college_name',
        'ug_university_board',
        'ug_college_city',
        'ug_college_district',
        'ug_roll_no',
        'ug_passed_year',
        'ug_marks_type',
        'ug_marks',
        'ug_cgpa',
        'ug_current_status',

        // PG
        'pg_college_name',
        'pg_university_board',
        'pg_college_city',
        'pg_college_district',
        'pg_roll_no',
        'pg_passed_year',
        'pg_marks_type',
        'pg_marks',
        'pg_cgpa',
        'pg_current_status',

        // Audit
        'log_userid',
        'log_datetime',
        'log_ipaddress',
    ];

    /**
     * Relationship to parent registration.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }
}

