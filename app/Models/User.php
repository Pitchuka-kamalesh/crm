<?php

namespace App\Models;

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
        return $this->hasOne(StudentRegistration::class, 'register_no', 'usercode');
    }

    public function getNameAttribute(): string
    {
        if ($this->user_type === 2 && $this->employee) {
            return $this->employee->full_name;
        }

        if ($this->user_type === 1) {
            if ($this->student) {
                return $this->student->student_name;
            }
            $student = StudentRegistration::where('register_no', $this->usercode)
                ->orWhere('application_no', $this->usercode)
                ->first();
            if ($student) {
                return $student->student_name;
            }
        }

        return $this->usercode;
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

        if ($this->employee && !empty($this->employee->role_id)) {
            return (int) $this->employee->role_id;
        }

        return $this->user_type === 2 ? 1 : 5;
    }

    public function getRoleLabelAttribute(): string
    {
        $roleId = $this->getActiveRoleId();
        try {
            $role = \Illuminate\Support\Facades\DB::table('master.roles')->where('role_id', $roleId)->first();
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
