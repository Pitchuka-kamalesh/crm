<?php

namespace Modules\Dashboards\Models;

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
        'tenth_school_state',
        'tenth_school_district',
        'tenth_roll_no',
        'tenth_passed_year',
        'tenth_marks_type',
        'tenth_current_status',
        'tenth_marks_conducted',
        'tenth_marks_earned',
        'tenth_percentage',

        // 12th / PUC / Diploma
        'pu2_college_name',
        'pu2_campus_location',
        'pu2_study_board',
        'pu2_board_of_study',
        'pu2_group',
        'pu2_college_city',
        'pu2_college_state',
        'pu2_college_district',
        'undertaking_60_pct',
        'pu2_roll_no',
        'pu2_passed_year',
        'pu2_marks_type',
        'pu2_marks',
        'pu2_marks_conducted',
        'pu2_marks_earned',
        'pu2_percentage',
        'pu2_current_status',

        // UG
        'ug_college_name',
        'ug_university_board',
        'ug_college_city',
        'ug_college_state',
        'ug_college_district',
        'ug_roll_no',
        'ug_passed_year',
        'ug_marks_type',
        'ug_marks',
        'ug_marks_conducted',
        'ug_marks_earned',
        'ug_percentage',
        'ug_current_status',

        // PG
        'pg_college_name',
        'pg_university_board',
        'pg_college_city',
        'pg_college_state',
        'pg_college_district',
        'pg_roll_no',
        'pg_passed_year',
        'pg_marks_type',
        'pg_marks',
        'pg_marks_conducted',
        'pg_marks_earned',
        'pg_percentage',
        'pg_current_status',
        'pg_study_type',
        'pg1_doc_type',
        'pg1_cmm_memo_no',
        'pg1_cmm_cgpa',
        'pg1_cmm_year',
        'pg1_pc_od_no',
        'pg1_pc_od_year',
        'pg1_pc_od_status',

        // PG 2 (for Ph.D / Doctoral)
        'pg2_college_name',
        'pg2_university_board',
        'pg2_college_city',
        'pg2_college_state',
        'pg2_college_district',
        'pg2_roll_no',
        'pg2_passed_year',
        'pg2_marks_type',
        'pg2_marks',
        'pg2_marks_conducted',
        'pg2_marks_earned',
        'pg2_percentage',
        'pg2_current_status',
        'pg2_study_type',
        'pg2_doc_type',
        'pg2_cmm_memo_no',
        'pg2_cmm_cgpa',
        'pg2_cmm_year',
        'pg2_pc_od_no',
        'pg2_pc_od_year',
        'pg2_pc_od_status',

        // Audit
        'log_userid',
        'log_datetime',
        'log_ipaddress',
    ];

    public function getTenthMarksAttribute()
    {
        return $this->attributes['tenth_marks_earned'] ?? $this->attributes['tenth_percentage'] ?? null;
    }

    public function getTenthCgpaAttribute()
    {
        if (!empty($this->attributes['tenth_percentage'])) {
            return round(((float)$this->attributes['tenth_percentage']) / 9.5, 2);
        }
        return $this->attributes['tenth_marks_earned'] ?? null;
    }

    public function getPu2CgpaAttribute()
    {
        if (!empty($this->attributes['pu2_percentage'])) {
            return round(((float)$this->attributes['pu2_percentage']) / 9.5, 2);
        }
        return null;
    }

    public function getUgCgpaAttribute()
    {
        if (!empty($this->attributes['ug_percentage'])) {
            return round(((float)$this->attributes['ug_percentage']) / 9.5, 2);
        }
        return $this->attributes['ug_marks'] ?? null;
    }

    public function getPgCgpaAttribute()
    {
        if (!empty($this->attributes['pg_percentage'])) {
            return round(((float)$this->attributes['pg_percentage']) / 9.5, 2);
        }
        return $this->attributes['pg_marks'] ?? null;
    }

    public function getPg2CgpaAttribute()
    {
        if (!empty($this->attributes['pg2_percentage'])) {
            return round(((float)$this->attributes['pg2_percentage']) / 9.5, 2);
        }
        return $this->attributes['pg2_marks'] ?? null;
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(\Modules\Crm\Models\CrmStudentRegistration::class, 'applino', 'applinano');
    }
}

