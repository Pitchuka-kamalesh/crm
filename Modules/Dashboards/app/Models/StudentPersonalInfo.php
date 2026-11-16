<?php

namespace Modules\Dashboards\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPersonalInfo extends Model
{
    use HasFactory;

    protected $table = 'crm.student_personal_info';
    protected $primaryKey = 'crmid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'crmid',
        'applino',
        'phoneno',
        'dob',
        'gender',
        'parent_name',
        'parentphono',
        'caste_category',
        'referedby',
        'referedempcode',
        'country',
        'state',
        'district',
        'city_mandal',
        'village',
        'pincode',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(\Modules\Crm\Models\CrmStudentRegistration::class, 'applino', 'applinano');
    }
}

