<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->belongsTo(\App\Models\Campus::class, 'campuscode', 'campus_code');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Program::class, 'programid', 'program_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branchid', 'branch_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Country::class, 'countryid', 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(\App\Models\State::class, 'stateid', 'state_id');
    }

    public function personalInfo(): HasOne
    {
        return $this->hasOne(\App\Models\StudentPersonalInfo::class, 'applino', 'applinano');
    }

    public function personal(): HasOne
    {
        return $this->personalInfo();
    }

    public function academicDetails(): HasOne
    {
        return $this->hasOne(\App\Models\StudentAcademicDetail::class, 'applino', 'applinano');
    }

    public function academic(): HasOne
    {
        return $this->academicDetails();
    }

    public function payDetail(): HasOne
    {
        return $this->hasOne(\Modules\Finance\Models\PayDetail::class, 'applino', 'applinano');
    }

    public function payDetails(): HasMany
    {
        return $this->hasMany(\Modules\Finance\Models\PayDetail::class, 'applino', 'applinano');
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(\Modules\Finance\Models\ReceiptDetail::class, 'applino', 'applinano');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(\Modules\Finance\Models\ReceiptDetail::class, 'applino', 'applinano');
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(\Modules\Finance\Models\Coupous::class, 'applino', 'applinano');
    }

    public function telecallerLead(): HasOne
    {
        return $this->hasOne(\App\Models\TelecallerLead::class, 'applino', 'applinano');
    }

    public function telecallerTasks(): HasMany
    {
        return $this->hasMany(\App\Models\TelecallerLeadTask::class, 'applino', 'applinano');
    }

    public function uploads(): HasOne
    {
        return $this->hasOne(\App\Models\StudentUpload::class, 'applino', 'applinano');
    }

    public function examSchedule(): HasOne
    {
        return $this->hasOne(\App\Models\ExamSchedule::class, 'applino', 'applinano');
    }

    // ==========================================
    // ELOQUENT QUERY SCOPES (REPLACES RAW SQL)
    // ==========================================

    public function scopePaid(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('stage1', 'paid')
              ->orWhereHas('payDetail', fn($p) => $p->where('txnstatus', 'success'))
              ->orWhereHas('receipt');
        });
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('stage6', 'completed');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }
        $clean = trim($term);
        return $query->where(function ($q) use ($clean) {
            $q->where('applinano', 'like', "%{$clean}%")
              ->orWhere('name', 'ilike', "%{$clean}%")
              ->orWhere('phoneno', 'like', "%{$clean}%")
              ->orWhere('emailid', 'ilike', "%{$clean}%")
              ->orWhere('crmid', 'like', "%{$clean}%");
        });
    }

    public function scopeForCampus(Builder $query, ?string $campusCode): Builder
    {
        if (empty($campusCode) || $campusCode === 'all') {
            return $query;
        }
        return $query->where('campuscode', $campusCode);
    }

    public function scopeForProgram(Builder $query, ?string $programId): Builder
    {
        if (empty($programId) || $programId === 'all') {
            return $query;
        }
        return $query->where('programid', $programId);
    }

    public function scopeForBranch(Builder $query, ?string $branchId): Builder
    {
        if (empty($branchId) || $branchId === 'all') {
            return $query;
        }
        return $query->where('branchid', $branchId);
    }

    public function scopeStagePending(Builder $query, int $step): Builder
    {
        $col = 'stage' . $step;
        return $query->where(function ($q) use ($col) {
            $q->whereNull($col)->orWhere($col, '!=', 'completed');
        });
    }

    public function scopeStageCleared(Builder $query, int $step): Builder
    {
        $col = 'stage' . $step;
        if ($step === 1) {
            return $this->scopePaid($query);
        }
        return $query->where($col, 'completed');
    }

    /**
     * Generate the next 10-digit application number starting from 2027000001.
     */
    public static function generateNextApplicationNumber(): string
    {
        try {
            $maxApp = self::where('applinano', 'like', '2027%')
                ->whereRaw('length(applinano) = 10')
                ->max('applinano');

            if ($maxApp && is_numeric($maxApp)) {
                return (string) ((int) $maxApp + 1);
            }
        } catch (\Throwable $e) {}

        return '2027000001';
    }
}

