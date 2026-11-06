<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'master.login';
    protected $primaryKey = 'usercode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'usercode',
        'phnno',
        'personalmail',
        'officialmail',
        'pwd',
        'pwd_changed_at',
        'user_type',
        'role_id',
        'campus_id',
        'designation_id',
        'department_id',
        'is_active',
        'last_login_at',
        'lock_status',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected $hidden = [
        'pwd',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'pwd_changed_at' => 'datetime',
            'log_timestamp' => 'datetime',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'pwd';
    }

    public function getAuthPassword(): ?string
    {
        return $this->pwd;
    }

    public function getAuthIdentifierName(): string
    {
        return 'usercode';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->usercode;
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // No-op: master.login table does not store remember_token
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'emp_code', 'usercode');
    }

    public function student(): HasOne
    {
        return $this->hasOne(CrmStudentRegistration::class, 'applinano', 'usercode');
    }

    public function getNameAttribute(): string
    {
        try {
            if ($this->user_type === 2 && $this->employee) {
                return $this->employee->full_name;
            }

            if ($this->user_type === 1) {
                if ($this->student && !empty($this->student->name)) {
                    return $this->student->name;
                }
                $crmStudent = CrmStudentRegistration::where('applinano', $this->usercode)
                    ->orWhere('phoneno', $this->phnno)
                    ->first();
                if ($crmStudent && !empty($crmStudent->name)) {
                    return $crmStudent->name;
                }
            }
        } catch (\Throwable $e) {
            // Gracefully fallback
        }

        return $this->usercode ?: 'Applicant';
    }

    public function getActiveRoleId(): int
    {
        $sessionRole = session('user.role_id');
        if (!empty($sessionRole)) {
            return (int) $sessionRole;
        }

        if (!empty($this->role_id)) {
            return (int) $this->role_id;
        }

        try {
            if ($this->employee && !empty($this->employee->role_id)) {
                return (int) $this->employee->role_id;
            }
        } catch (\Throwable $e) {}

        // Predefined staff role mapping without touching database tables
        if ($this->usercode === '03141' || str_contains($this->officialmail ?? '', 'dean')) {
            return 3; // CRM DEAN
        }

        if ($this->usercode === '03434' || str_contains($this->officialmail ?? '', 'mgt')) {
            return 2; // CRM Incharge
        }

        return $this->user_type === 2 ? 1 : 5;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function getRoleLabelAttribute(): string
    {
        $roleId = $this->getActiveRoleId();
        try {
            $role = Role::find($roleId);
            if ($role && !empty($role->role_name)) {
                return $role->role_name;
            }
        } catch (\Throwable $e) {}

        return match ($this->user_type) {
            2 => 'Employee',
            1 => 'Student',
            default => 'User',
        };
    }
}
