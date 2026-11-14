<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        // master.login table does not store remember_token
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function employee(): HasOne
    {
        return $this->hasOne(\Modules\Auth\Models\Employee::class, 'emp_code', 'usercode');
    }

    public function student(): HasOne
    {
        return $this->hasOne(\Modules\Crm\Models\CrmStudentRegistration::class, 'applinano', 'usercode');
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
                $crmStudent = \Modules\Crm\Models\CrmStudentRegistration::where('applinano', $this->usercode)
                    ->orWhere('phoneno', $this->phnno)
                    ->first();
                if ($crmStudent && !empty($crmStudent->name)) {
                    return $crmStudent->name;
                }
            }
        } catch (\Throwable $e) {}

        if ($this->usercode && $this->usercode !== '0') {
            return $this->usercode;
        }

        return $this->phnno ?: 'Applicant';
    }

    public function roleAssigned(): HasOne
    {
        return $this->hasOne(\Modules\Auth\Models\RoleAssigned::class, 'emp_id', 'usercode');
    }

    public function assignedRoles(): HasMany
    {
        return $this->hasMany(\Modules\Auth\Models\RoleAssigned::class, 'emp_id', 'usercode');
    }

    /**
     * Retrieve all active role IDs assigned to this user in master.roles_assigned.
     */
    public function getAssignedRoleIds(): array
    {
        if (empty($this->usercode) || $this->usercode === '0') {
            return !empty($this->role_id) ? [(int) $this->role_id] : [];
        }

        try {
            $ids = \Modules\Auth\Models\RoleAssigned::where('emp_id', $this->usercode)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->orderBy('roles_role_id')
                ->pluck('roles_role_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values()
                ->all();

            if (!empty($ids)) {
                return $ids;
            }
        } catch (\Throwable $e) {}

        return !empty($this->role_id) ? [(int) $this->role_id] : [];
    }

    public function getActiveRoleId(): int
    {
        $allowedRoleIds = $this->getAssignedRoleIds();

        $sessionRole = session('user.role_id');
        if (!empty($sessionRole)) {
            $sessionRoleId = (int) $sessionRole;
            if (in_array($sessionRoleId, $allowedRoleIds, true)) {
                return $sessionRoleId;
            }
            try {
                session()->forget('user.role_id');
            } catch (\Throwable $e) {}
        }

        if (!empty($allowedRoleIds)) {
            return (int) $allowedRoleIds[0];
        }

        if (!empty($this->role_id)) {
            return (int) $this->role_id;
        }

        try {
            $assigned = \Modules\Auth\Models\RoleAssigned::where('emp_id', $this->usercode)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->orderBy('roles_role_id')
                ->first();

            if ($assigned && !empty($assigned->roles_role_id)) {
                return (int) $assigned->roles_role_id;
            }
        } catch (\Throwable $e) {}

        try {
            if ($this->employee && !empty($this->employee->role_id)) {
                return (int) $this->employee->role_id;
            }
        } catch (\Throwable $e) {}

        return $this->user_type === 2 ? 1 : 5;
    }

    public function getAssignedRoles(): \Illuminate\Support\Collection
    {
        $assignedRoleIds = $this->getAssignedRoleIds();
        return \Modules\Auth\Models\Role::whereIn('role_id', $assignedRoleIds)->orderBy('role_id')->get();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\Role::class, 'role_id', 'role_id');
    }

    public function getRoleLabelAttribute(): string
    {
        $roleId = $this->getActiveRoleId();
        try {
            $role = \Modules\Auth\Models\Role::find($roleId);
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

