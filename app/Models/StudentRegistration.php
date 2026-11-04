<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRegistration extends Model
{
    protected $table = 'admissions.student_registrations';
    protected $primaryKey = 'application_no';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'application_no',
        'enrollment_no',
        'register_no',
        'student_name',
        'personal_email',
        'dob',
        'gender',
        'doj',
        'mobile',
        'ofc_mail',
        'regulation',
        'progid',
        'electiveid',
        'admyear',
        'adm_batch_code',
        'aadhar_no',
        'abc_id',
        'deb_id',
        'feeid',
        'std_type',
        'std_status',
        'lock_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'progid', 'program_id');
    }
}
