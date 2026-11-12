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
    protected $primaryKey = 'phnno';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'usercode',
        'phnno',
        'aadharno',
        'dob',
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
        return 'phnno';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->phnno;
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

        if ($this->usercode && $this->usercode !== '0') {
            return $this->usercode;
        }

        return $this->phnno ?: 'Applicant';
    }

    /**
     * Strict Mode: Retrieve raw role IDs assigned to this user in master.roles_assigned.
     *
     * @return array<int>
     */
    public function getAssignedRoleIds(): array
    {
        if ($this->user_type === 1 || (int)$this->role_id === 5) {
            return [5];
        }

        $empCode = $this->usercode;
        if (empty($empCode) || $empCode === '0') {
            try {
                $emp = Employee::where('phone_primary', $this->phnno)
                    ->orWhere('phone_secondary', $this->phnno)
                    ->first();
                $empCode = $emp?->emp_code;
            } catch (\Throwable $e) {}
        }

        if (!empty($empCode)) {
            try {
                $assigned = RoleAssigned::where('emp_id', $empCode)
                    ->where(function ($q) {
                        $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                    })
                    ->orderBy('roles_role_id')
                    ->pluck('roles_role_id')
                    ->map(fn($id) => (int)$id)
                    ->toArray();

                if (!empty($assigned)) {
                    return array_values(array_unique($assigned));
                }
            } catch (\Throwable $e) {}
        }

        if (!empty($this->role_id)) {
            return [(int)$this->role_id];
        }

        try {
            if ($this->employee && !empty($this->employee->role_id)) {
                return [(int)$this->employee->role_id];
            }
        } catch (\Throwable $e) {}

        return [$this->user_type === 2 ? 1 : 5];
    }

    public function getActiveRoleId(): int
    {
        $allowedRoleIds = $this->getAssignedRoleIds();

        // Option A (Strict Mode): Only honor session role if employee is actually assigned that role
        $sessionRole = session('user.role_id');
        if (!empty($sessionRole)) {
            $sessionRoleId = (int) $sessionRole;
            if (in_array($sessionRoleId, $allowedRoleIds, true)) {
                return $sessionRoleId;
            }
            // Clear unauthorized or stale session role
            try {
                session()->forget('user.role_id');
            } catch (\Throwable $e) {}
        }

        // Return first assigned role from master.roles_assigned
        if (!empty($allowedRoleIds)) {
            return (int) $allowedRoleIds[0];
        }

        if (!empty($this->role_id)) {
            return (int) $this->role_id;
        }

        try {
            if ($this->employee && !empty($this->employee->role_id)) {
                return (int) $this->employee->role_id;
            }
        } catch (\Throwable $e) {}

        return $this->user_type === 2 ? 1 : 5;
    }

    /**
     * Strict Mode: Retrieve only roles actually assigned to this user in master.roles_assigned.
     */
    public function getAssignedRoles(): \Illuminate\Support\Collection
    {
        $assignedRoleIds = $this->getAssignedRoleIds();
        return Role::whereIn('role_id', $assignedRoleIds)->orderBy('role_id')->get();
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
