<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentUpload extends Model
{
    use HasFactory;

    protected $table = 'crm.student_uploads';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'crmid',
        'applino',
        'phoneno',

        'tenth_file_name',
        'tenth_file_path',

        'inter_file_name',
        'inter_file_path',

        'btech_file_name',
        'btech_file_path',

        'mtech_file_name',
        'mtech_file_path',

        'pg_file_name',
        'pg_file_path',

        'aadhar_file_name',
        'aadhar_file_path',

        'passport_file_name',
        'passport_file_path',

        'transfer_file_name',
        'transfer_file_path',

        'study_conduct_file_name',
        'study_conduct_file_path',

        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    /**
     * Relationship to registration.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }
}

