<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $q->whereIn(DB::raw('LOWER(TRIM(stage4))'), ['paid', 'completed'])
              ->orWhere('stage1', 'paid')
              ->orWhereHas('payDetail', fn($p) => $p->where('txnstatus', 'success')->orWhere('paidamount', '>', 0))
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
     * Calculate academic year details and application number prefix based on timestamp.
     *
     * Academic cycle:
     * - June to December (months 6–12): Academic Year is Y–(Y+1) (e.g. Nov 2026 => 2026–2027, prefix 2027)
     * - January to May (months 1–5): Academic Year is (Y-1)–Y (e.g. Feb 2027 => 2026–2027, prefix 2027)
     *
     * @param \DateTimeInterface|null $date
     * @return array{start_year: int, end_year: int, prefix: string, academic_year_short: string, academic_year_full: string}
     */
    public static function getAcademicYearInfo(?\DateTimeInterface $date = null): array
    {
        $date = $date ?? now();
        $month = (int) $date->format('n');
        $year = (int) $date->format('Y');

        $startYear = ($month >= 6) ? $year : ($year - 1);
        $endYear = $startYear + 1;

        return [
            'start_year'          => $startYear,
            'end_year'            => $endYear,
            'prefix'              => (string) $endYear,
            'academic_year_short' => $startYear . '–' . substr((string) $endYear, -2),
            'academic_year_full'  => $startYear . '–' . $endYear,
        ];
    }

    /**
     * Generate the next 10-digit application number based on timestamp (e.g. starting with 2027 for AY 2026-2027).
     * Guarantees absolute uniqueness across both crm.student_registration and master.login.
     *
     * @param \DateTimeInterface|null $date
     * @return string
     */
    public static function generateNextApplicationNumber(?\DateTimeInterface $date = null): string
    {
        try {
            $ay = self::getAcademicYearInfo($date);
            $prefix = $ay['prefix']; // e.g. "2027"
            $minRange = (int) ($prefix . '000000');
            $maxRange = (int) ($prefix . '899999'); // Exclude test/sentinel 900000+ numbers

            $maxAppInCrm = (int) self::where('applinano', 'like', $prefix . '%')
                ->whereRaw('length(applinano) = 10')
                ->whereRaw("applinano ~ '^[0-9]+$'")
                ->whereRaw("applinano::bigint between ? and ?", [$minRange, $maxRange])
                ->max('applinano');

            $maxUserInLogin = (int) DB::table('master.login')
                ->where('usercode', 'like', $prefix . '%')
                ->whereRaw('length(usercode) = 10')
                ->whereRaw("usercode ~ '^[0-9]+$'")
                ->whereRaw("usercode::bigint between ? and ?", [$minRange, $maxRange])
                ->max('usercode');

            $next = max($maxAppInCrm, $maxUserInLogin, $minRange) + 1;

            // Ensure strictly unique against both master.login and crm.student_registration
            while (
                self::where('applinano', (string) $next)->exists() ||
                DB::table('master.login')->where('usercode', (string) $next)->exists()
            ) {
                $next++;
                if ($next > $maxRange) {
                    break;
                }
            }

            return (string) $next;
        } catch (\Throwable $e) {
            Log::error('[CrmStudentRegistration] generateNextApplicationNumber error: ' . $e->getMessage());
        }

        $fallbackPrefix = self::getAcademicYearInfo($date)['prefix'];
        return $fallbackPrefix . '000001';
    }
}

